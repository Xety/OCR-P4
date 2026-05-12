<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;

final class ErrorController extends AbstractController
{
    /**
     * Affiche la page 404.
     */
    public function notFound(Request $request): string
    {
        http_response_code(404);

        return $this->view->render('errors/404', [
            'title' => '404 — Page introuvable',
            'uri' => $request->uri,
        ]);
    }
}
