<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\RendererInterface;
use App\Core\Request;
use PDO;

/**
 * Contrôleur pour les pages.
 *
 * Reçoit ses dépendances par injection de constructeur.
 */
final class PageController
{
    public function __construct(
        private readonly RendererInterface $view,
        private readonly PDO $db,
    ) {}

    /**
     * Affiche la page d'accueil.
     */
    public function index(Request $request): string
    {
        return $this->view->render('pages/home', [
            'title' => 'Accueil'
        ]);
    }
}
