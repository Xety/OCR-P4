<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Redirect;
use App\Core\Request;
use App\Repositories\UserRepository;

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

        if ($email === '' || $password === '') {
            return $this->view->render('auth/login', [
                'title' => 'Connexion',
                'mainClass' => 'main--full',
                'error' => 'Veuillez remplir tous les champs.',
            ]);
        }

        $user = (new UserRepository($this->db))->findByEmail($email);

        // Comparaison constante pour éviter les timing attacks (OWASP A02)
        if ($user === null || ! password_verify($password, $user->passwordHash ?? '')) {
            return $this->view->render('auth/login', [
                'title'     => 'Connexion',
                'mainClass' => 'main--full',
                'error'     => 'Identifiants incorrects.',
            ]);
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
}
