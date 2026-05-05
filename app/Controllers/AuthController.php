<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Redirect;
use App\Core\Request;
use App\Repositories\UserRepository;
use App\Validation\Rules\Email;
use App\Validation\Rules\MinLength;
use App\Validation\Rules\Confirmed;
use App\Validation\Rules\Required;
use App\Validation\Validator;

/**
 * Gère l'authentification : formulaire de connexion, traitement POST et déconnexion.
 */
final class AuthController extends AbstractController
{

    /**
     * Affiche le formulaire de connexion.
     * Redirige vers / si l'utilisateur est déjà connecté.
     */
    public function showLogin(Request $request): string
    {
        if (Auth::isAuthenticated()) {
            Redirect::to('/');
        }

        return $this->view->render('auth/login', [
            'title' => 'Connexion',
            'mainClass' => 'main--full',
        ]);
    }

    /**
     * Traite le formulaire de connexion (POST /login).
     *
     * Vérifie les identifiants, puis connecte l'utilisateur.
     *
     * Redirige vers / en cas de succès, ou réaffiche le formulaire avec une erreur en cas d'échec.
     *
     * @return mixed
     */
    public function login(Request $request): string
    {
        if (Auth::isAuthenticated()) {
            Redirect::to('/');
        }

        $email = trim($request->body['email'] ?? '');
        $password = $request->body['password'] ?? '';

        // Fonction de rendu en cas d'erreur, pour éviter la duplication de code
        $renderError = function (string $error) use ($email): string {
            return $this->view->render('auth/login', [
                'title' => 'Connexion',
                'mainClass' => 'main--full',
                'error' => $error,
                'old' => ['email' => $email],
            ]);
        };

        $validator = new Validator($request->body, [
            'email'    => [new Required(), new Email()],
            'password' => [new Required()],
        ]);

        if ($validator->fails()) {
            return $renderError($validator->firstError());
        }

        $user = (new UserRepository($this->db))->findByEmail($email);

        // Comparaison constante pour éviter les timing attacks (OWASP A02)
        if ($user === null || ! password_verify($password, $user->password ?? '')) {
            return $renderError('Identifiants invalides.');
        }

        Auth::login($user);

        Redirect::to('/');
    }

    /**
     * Déconnecte l'utilisateur et redirige vers la page de connexion.
     */
    public function logout(Request $request): never
    {
        Auth::logout();
        Redirect::to('/login');
    }

    /**
     * Affiche le formulaire d'inscription.
     * Redirige vers / si l'utilisateur est déjà connecté.
     */
    public function showRegister(Request $request): string
    {
        if (Auth::isAuthenticated()) {
            Redirect::to('/');
        }

        return $this->view->render('auth/register', [
            'title' => 'Inscription',
            'mainClass' => 'main--full',
        ]);
    }

    /**
     * Traite le formulaire d'inscription (POST /register).
     *
     * Valide les champs, vérifie l'unicité de l'email,
     * crée le compte et connecte l'utilisateur.
     *
     * @return string Le HTML à afficher (formulaire avec erreurs ou redirection)
     */
    public function register(Request $request): string
    {
        if (Auth::isAuthenticated()) {
            Redirect::to('/');
        }

        $name = trim($request->body['name'] ?? '');
        $email = trim($request->body['email'] ?? '');
        $password = $request->body['password'] ?? '';

        // Fonction de rendu en cas d'erreur, pour éviter la duplication de code
        $renderError = function (string $error) use ($name, $email): string {
            return $this->view->render('auth/register', [
                'title' => 'Inscription',
                'mainClass' => 'main--full',
                'error' => $error,
                'old' => ['name' => $name, 'email' => $email],
            ]);
        };

        // Validation des champs
        $validator = new Validator($request->body, [
            'name' => [new Required()],
            'email' => [new Required(), new Email()],
            'password' => [new Required(), new MinLength(8)],
            'password_confirmation' => [new Confirmed('password')],
        ]);

        if ($validator->fails()) {
            return $renderError($validator->firstError());
        }

        $repo = new UserRepository($this->db);

        if ($repo->findByEmail($email) !== null) {
            return $renderError('Cette adresse email est déjà utilisée.');
        }

        $user = $repo->create($name, $email, $password);

        Auth::login($user);

        Redirect::to('/');
    }
}
