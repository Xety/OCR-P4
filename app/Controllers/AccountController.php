<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helpers\DateHelper;
use App\Core\Redirect;
use App\Core\Request;
use App\Repositories\BookRepository;
use App\Repositories\UserRepository;
use App\Validation\Rules\Email;
use App\Validation\Rules\MinLength;
use App\Validation\Rules\Required;
use App\Validation\Validator;

final class AccountController extends AbstractController
{
    /**
     * Affiche la page Mon compte.
     *
     * @return string
     */
    public function index(Request $request): string
    {
        $this->requireAuth();

        $authData = Auth::user();
        $bookRepo = new BookRepository($this->db);

        $books = $bookRepo->findByUserId($authData['id']);

        $userRepo = new UserRepository($this->db);
        $user = $userRepo->find((int) $authData['id']);
        $memberSince = DateHelper::elapsed($user->getCreatedAt());

        // Récupération du message flash éventuel
        $success = null;
        if (isset($_SESSION['flash_success'])) {
            $success = $_SESSION['flash_success'];
            unset($_SESSION['flash_success']);
        }

        return $this->view->render('account/index', [
            'title' => 'Mon compte',
            'user' => $user,
            'books' => $books,
            'memberSince' => $memberSince,
            'success' => $success,
        ]);
    }

    /**
     * Met à jour le profil de l'utilisateur.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     *
     * @return string La réponse HTTP à afficher.
     */
    public function update(Request $request): string
    {
        $this->requireAuth();

        $authData = Auth::user();
        $userId = (int) $authData['id'];

        $name = trim($request->body['name'] ?? '');
        $email = trim($request->body['email'] ?? '');
        $password = $request->body['password'] ?? '';

        $rules = [
            'name'  => [new Required()],
            'email' => [new Required(), new Email()],
        ];

        // Le mot de passe est facultatif : on ne le valide que s'il est renseigné
        if ($password !== '') {
            $rules['password'] = [new MinLength(8)];
        }

        $validator = new Validator($request->body, $rules);

        $userRepo = new UserRepository($this->db);
        $user = $userRepo->find($userId);
        $books = (new BookRepository($this->db))->findByUserId($userId);

        $memberSince = DateHelper::elapsed($user->getCreatedAt());

        $renderError = function (string $error) use ($user, $books, $memberSince, $name, $email): string {
            return $this->view->render('account/index', [
                'title'=> 'Mon compte',
                'user'=> $user,
                'books'=> $books,
                'memberSince'=> $memberSince,
                'error'=> $error,
                'old'=> ['name'=> $name, 'email'=> $email],
            ]);
        };

        if ($validator->fails()) {
            return $renderError($validator->firstError());
        }

        // Vérification d'unicité de l'email si modifié
        if ($email !== $user->getEmail()) {
            $existing = $userRepo->findByEmail($email);
            if ($existing !== null && $existing->getId() !== $userId) {
                return $renderError('Cette adresse email est déjà utilisée.');
            }
        }

        $user->fill([
            'name' => $name,
            'email' => $email,
        ]);

        $userRepo->save($user);

        if ($password !== '') {
            $userRepo->updatePassword($userId, password_hash($password, PASSWORD_BCRYPT));
        }

        // Mise à jour de la session avec les nouvelles données
        $updatedUser = $userRepo->find($userId);
        Auth::login($updatedUser);

        $_SESSION['flash_success'] = 'Votre profil a été mis à jour.';
        Redirect::to('/account');
    }
}
