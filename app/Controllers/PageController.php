<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;

/**
 * Contrôleur pour les pages.
 */
final class PageController extends AbstractController
{

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
