<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Repositories\BookRepository;

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
        $bookRepo = new BookRepository($this->db);
        $latestBooks = $bookRepo->findLatest(4);

        return $this->view->render('pages/index', [
            'title' => 'Accueil',
            'mainClass' => 'main--full',
            'latestBooks' => $latestBooks,
        ]);
    }
}
