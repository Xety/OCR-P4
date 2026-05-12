<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Paginator;
use App\Core\Redirect;
use App\Core\Request;
use App\Repositories\BookRepository;

final class BookController extends AbstractController
{
    /**
     * Affiche la liste paginée de tous les livres.
     */
    public function index(Request $request): string
    {
        $search  = trim($request->query['q'] ?? '');
        $perPage = (int) config('pagination.books', 16);

        $bookRepo = new BookRepository($this->db);
        $total = $bookRepo->countAll($search);

        $paginator = new Paginator(
            page: max(1, (int) ($request->query['page'] ?? 1)),
            perPage: $perPage,
            total: $total,
        );

        if ($paginator->isOutOfBounds()) {
            Redirect::to('/books');
        }

        $books   = $bookRepo->findAllPaginated($paginator->getPerPage(), $paginator->getOffset(), $search);
        $baseUrl = $search !== '' ? '/books?q=' . urlencode($search) : '/books';

        return $this->view->render('pages/books', [
            'title' => "Nos livres à l'échange",
            'books' => $books,
            'search' => $search,
            'paginator' => $paginator,
            'baseUrl' => $baseUrl,
        ]);
    }

    /**
     * Affiche le détail d'un livre.
     */
    public function show(Request $request): string
    {
        $id = (int) ($request->params['id'] ?? 0);

        $bookRepo = new BookRepository($this->db);
        $book = $bookRepo->findById($id);

        if ($book === null) {
            Redirect::to('/books');
        }

        return $this->view->render('pages/book', [
            'title' => e($book->getTitle()) . ' — Livre à l\'échange',
            'mainClass' => 'main--full',
            'book' => $book,
        ]);
    }
}
