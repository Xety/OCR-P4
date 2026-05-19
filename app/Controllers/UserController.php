<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Helpers\DateHelper;
use App\Core\Request;
use App\Repositories\BookRepository;
use App\Repositories\UserRepository;

final class UserController extends AbstractController
{
    public function show(Request $request): string
    {
        $id = (int) ($request->params['id'] ?? 0);

        $userRepo = new UserRepository($this->db);
        $user = $userRepo->find($id);

        if ($user === null) {
            http_response_code(404);

            return $this->view->render('errors/404', [
                'title' => '404 — Page introuvable',
                'uri' => $request->uri,
            ]);
        }

        $bookRepo = new BookRepository($this->db);
        $books = $bookRepo->findByUserId($id);
        $memberSince = DateHelper::elapsed($user->getCreatedAt());

        $authData = Auth::user();
        $authId   = $authData !== null ? (int) $authData['id'] : null;

        return $this->view->render('pages/user', [
            'title'       => e($user->getName()) . ' — Profil',
            'user'        => $user,
            'books'       => $books,
            'memberSince' => $memberSince,
            'authId'      => $authId,
        ]);
    }
}