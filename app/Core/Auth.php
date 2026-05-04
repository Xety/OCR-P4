<?php

declare(strict_types=1);

namespace App\Core;

use App\Entities\UserEntity;

/**
 * Gestion de l'authentification via session PHP.
 *
 * Classe utilitaire statique : démarre la session, connecte/déconnecte
 * un utilisateur et expose les données de l'utilisateur courant.
 */
final class Auth
{
    private const string SESSION_KEY = 'auth_user';

    private static bool $started = false;

    private function __construct() {}

    /**
     * Démarre la session PHP si ce n'est pas déjà fait.
     */
    public static function start(): void
    {
        if (! self::$started && session_status() === PHP_SESSION_NONE) {
            session_start();
            self::$started = true;
        }
    }

    /**
     * Connecte un utilisateur : stocke ses données en session
     * et régénère l'ID de session pour prévenir la fixation de session.
     */
    public static function login(UserEntity $user): void
    {
        self::start();
        session_regenerate_id(delete_old_session: true);

        $_SESSION[self::SESSION_KEY] = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    /**
     * Déconnecte l'utilisateur et détruit intégralement la session.
     */
    public static function logout(): void
    {
        self::start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                name: session_name(),
                value: '',
                expires_or_options: time() - 42000,
                path: $params['path'],
                domain: $params['domain'],
                secure: $params['secure'],
                httponly: $params['httponly'],
            );
        }

        session_destroy();
        self::$started = false;
    }

    /**
     * Indique si un utilisateur est connecté.
     */
    public static function isAuthenticated(): bool
    {
        self::start();

        return isset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Retourne les données de l'utilisateur connecté, ou null.
     *
     * @return array{id: int, name: string, email: string}|null
     */
    public static function user(): ?array
    {
        self::start();

        return $_SESSION[self::SESSION_KEY] ?? null;
    }
}
