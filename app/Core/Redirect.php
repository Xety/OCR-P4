<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Utilitaire de redirection HTTP.
 */
final class Redirect
{
    private function __construct() {}

    /**
     * Redirige vers l'URL donnée et arrête l'exécution.
     *
     * @param string $url L'URL vers laquelle rediriger.
     * @param int $statusCode 302 Le statut HTTP de la redirection (par défaut 302 Found).
     *
     * @return never Indique que cette fonction ne retourne jamais (car elle termine l'exécution après la redirection)
     */
    public static function to(string $url, int $statusCode = 302): never
    {
        http_response_code($statusCode);
        header('Location: ' . $url);
        exit;
    }
}
