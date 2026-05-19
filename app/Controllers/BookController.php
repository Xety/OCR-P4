<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Redirect;
use App\Core\Request;
use App\Entities\BookEntity;
use App\Repositories\BookRepository;
use App\Validation\Rules\{AllowedExtensions,Boolean, MaxFileSize, Required, UploadNoError};
use App\Validation\Validator;

final class BookController extends AbstractController
{
    /**
     * Affiche la liste paginée de tous les livres.
     *
     * @param Request $request
     *
     * @return string
     */
    public function index(Request $request): string
    {
        $search  = trim($request->query['q'] ?? '');

        $bookRepo = new BookRepository($this->db);
        $books = $bookRepo->findAllWithSearch($search);

        return $this->view->render('books/index', [
            'title' => "Nos livres à l'échange",
            'books' => $books,
            'search' => $search,
        ]);
    }

    /**
     * Affiche le détail d'un livre.
     *
     * @param Request $request
     *
     * @return string
     */
    public function show(Request $request): string
    {
        $id = (int) ($request->params['id'] ?? 0);

        $bookRepo = new BookRepository($this->db);
        $book = $bookRepo->findById($id);

        if ($book === null) {
            Redirect::to('/books');
        }

        $authData = Auth::user();
        $authId   = $authData !== null ? (int) $authData['id'] : null;

        return $this->view->render('books/show', [
            'title' => e($book->getTitle()) . ' — Livre à l\'échange',
            'mainClass' => 'main--full',
            'book' => $book,
            'authId' => $authId,
        ]);
    }


    /**
     * Affiche le formulaire de création d'un livre.
     */
    public function create(Request $request): string
    {
        $this->requireAuth();

        return $this->view->render('books/create', [
            'title' => 'Ajouter un livre',
            'old'   => [],
            'error' => null,
        ]);
    }

    /**
     * Traite la soumission du formulaire de création d'un livre.
     */
    public function store(Request $request): string
    {
        $this->requireAuth();

        $title       = trim($request->body['title'] ?? '');
        $author      = trim($request->body['author'] ?? '');
        $description = trim($request->body['description'] ?? '');
        $isAvailable = ($request->body['is_available'] ?? '1') === '1';

        $validator = new Validator($request->body, [
            'title'        => [new Required()],
            'author'       => [new Required()],
            'description'  => [new Required()],
            'is_available' => [new Boolean()],
        ]);

        $renderError = function (string $error) use ($title, $author, $description, $isAvailable): string {
            return $this->view->render('books/create', [
                'title' => 'Ajouter un livre',
                'error' => $error,
                'old'   => [
                    'title'        => $title,
                    'author'       => $author,
                    'description'  => $description,
                    'is_available' => $isAvailable ? '1' : '0',
                ],
            ]);
        };

        if ($validator->fails()) {
            return $renderError($validator->firstError());
        }

        $authData = Auth::user();
        $book = new BookEntity();
        $book->fill([
            'userId'      => (int) $authData['id'],
            'title'       => $title,
            'author'      => $author,
            'description' => $description,
            'isAvailable' => $isAvailable,
        ]);

        $bookRepo = new BookRepository($this->db);
        $book = $bookRepo->save($book);

        // Gestion de l'upload photo (après save pour avoir l'ID)
        try {
            $photo = $this->handlePhotoUpload($book);
        } catch (\RuntimeException $e) {
            $bookRepo->delete($book);
            return $renderError($e->getMessage());
        }

        if ($photo !== null) {
            $book->fill(['photo' => $photo]);
            $bookRepo->save($book);
        }

        $_SESSION['flash_success'] = 'Livre ajouté avec succès.';
        Redirect::to('/account');
    }

    /**
     * Affiche le formulaire d'édition d'un livre.
     */
    public function edit(Request $request): string
    {
        $this->requireAuth();

        $id = (int) ($request->params['id'] ?? 0);
        $bookRepo = new BookRepository($this->db);
        $book = $bookRepo->find($id);

        if ($book === null) {
            Redirect::to('/account');
        }

        $this->requireOwner($book);

        return $this->view->render('books/edit', [
            'title' => 'Modifier — ' . e($book->getTitle()),
            'book' => $book,
            'old' => [],
            'error' => null,
        ]);
    }

    /**
     * Traite la soumission du formulaire d'édition d'un livre.
     *
     * @param Request $request
     *
     * @return string
     */
    public function update(Request $request): string
    {
        $this->requireAuth();

        // Récupération du livre et vérification de l'existence
        $id = (int) ($request->params['id'] ?? 0);
        $bookRepo = new BookRepository($this->db);
        $book = $bookRepo->find($id);

        if ($book === null) {
            Redirect::to('/account');
        }

        // Seul le propriétaire du livre peut le modifier
        $this->requireOwner($book);

        $title = trim($request->body['title'] ?? '');
        $author = trim($request->body['author'] ?? '');
        $description = trim($request->body['description'] ?? '');
        $isAvailable = ($request->body['is_available'] ?? '0') === '1';

        $validator = new Validator($request->body, [
            'title' => [new Required()],
            'author' => [new Required()],
            'description' => [new Required()],
            'is_available' => [new Boolean()],
        ]);

        $renderError = function (string $error) use ($book, $title, $author, $description, $isAvailable): string {
            return $this->view->render('books/edit', [
                'title' => 'Modifier — ' . e($book->getTitle()),
                'book' => $book,
                'error' => $error,
                'old' => [
                    'title' => $title,
                    'author' => $author,
                    'description' => $description,
                    'is_available' => $isAvailable,
                ],
            ]);
        };

        if ($validator->fails()) {
            return $renderError($validator->firstError());
        }

        // Gestion de l'upload photo
        try {
            $photo = $this->handlePhotoUpload($book);
        } catch (\RuntimeException $e) {
            return $renderError($e->getMessage());
        }

        $book->fill([
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'isAvailable' => $isAvailable,
            'photo' => $photo,
        ]);

        $bookRepo->save($book);

        $_SESSION['flash_success'] = 'Livre mis à jour avec succès.';
        Redirect::to('/account');
    }

    /**
     * Supprime un livre de la collection de l'utilisateur courant.
     *
     * @param Request $request
     *
     * @return string
     */
    public function destroy(Request $request): string
    {
        $this->requireAuth();

        $id = (int) ($request->params['id'] ?? 0);
        $bookRepo = new BookRepository($this->db);
        $book = $bookRepo->find($id);

        if ($book === null) {
            Redirect::to('/account');
        }

        $this->requireOwner($book);

        $photo = $book->getPhoto();

        $deleted = $bookRepo->delete($book);

        // Suppression de la photo si elle existe
        if ($deleted && $photo !== null) {
            $photoPath = dirname(__DIR__, 2) . '/public/images/books/' . $photo;
            if (is_file($photoPath)) {
                unlink($photoPath);
            }
        }

        $_SESSION['flash_success'] = 'Livre supprimé avec succès.';
        Redirect::to('/account');
    }

    /**
     * Gère l'upload de la photo d'un livre, en validant le fichier et en supprimant l'ancienne photo si nécessaire.
     *
     * @param BookEntity $book Le livre en cours de modification
     *
     * @return string|null Le nom du fichier de la nouvelle photo, ou null si aucune
     *
     * @throws \RuntimeException
     */
    private function handlePhotoUpload(BookEntity $book): ?string
    {
        $uploadedFile = $_FILES['photo'] ?? null;
        // Si aucun fichier n'est uploadé, on conserve la photo actuelle
        if ($uploadedFile === null || $uploadedFile['error'] === UPLOAD_ERR_NO_FILE) {
            return $book->getPhoto();
        }

        $validator = new Validator($_FILES, [
            'photo' => [
                new UploadNoError(),
                new MaxFileSize(5),
                new AllowedExtensions(['jpg', 'jpeg', 'png', 'webp']),
            ],
        ]);
        if ($validator->fails()) {
            throw new \RuntimeException($validator->firstError());
        }

        // Génération d'un nom de fichier unique et déplacement du fichier uploadé
        $ext = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        $filename = uniqid('book_', more_entropy: true) . '.' . $ext;
        $dest = dirname(__DIR__, 2) . '/public/images/books/' . $filename;

        if (! move_uploaded_file($uploadedFile['tmp_name'], $dest)) {
            throw new \RuntimeException('Erreur lors de l\'enregistrement de la photo.');
        }

        $oldPhoto = $book->getPhoto();
        if ($oldPhoto !== null) {
            $oldPath = dirname(__DIR__, 2) . '/public/images/books/' . $oldPhoto;
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        return $filename;
    }

    /**
     * Redirige vers /account si l'utilisateur courant n'est pas le propriétaire du livre.
     *
     * @param BookEntity $book Le livre à vérifier
     *
     * @return void
     */
    private function requireOwner(BookEntity $book): void
    {
        $authData = Auth::user();

        if ($book->getUserId() !== (int) $authData['id']) {
            Redirect::to('/account');
        }
    }
}
