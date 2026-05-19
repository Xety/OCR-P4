<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\RendererInterface;
use App\Core\Auth;
use App\Core\Redirect;
use App\Repositories\UserRepository;
use PDO;

/**
 * Classe de base pour tous les contrôleurs.
 *
 * Centralise l'injection des dépendances communes afin d'éviter de les
 * répéter dans chaque contrôleur (principe DRY + principe D de SOLID).
 */
abstract class AbstractController
{
    public function __construct(
        protected readonly RendererInterface $view,
        protected readonly PDO $db,
    ) {}

    /**
     * Redirige vers /login si l'utilisateur n'est pas authentifié.
     */
    protected function requireAuth(): void
    {
        if (! Auth::isAuthenticated()) {
            Redirect::to('/login');
        }

        $authData = Auth::user();
        $userRepo = new UserRepository($this->db);

        if ($userRepo->find((int) $authData['id']) === null) {
            Auth::logout();
            Redirect::to('/login');
        }
    }
}
