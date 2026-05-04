<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Fournit une connexion PDO PostgreSQL unique (Singleton).
 */
final class Database
{
    /**
     *  L'instance PDO unique.
     *
     * @var PDO|null
     */
    private static ?PDO $instance = null;

    /**
     * Le constructeur est privé pour empêcher l'instanciation directe.
     */
    private function __construct() {}

    /**
     * Retourne l'instance PDO unique, en la créant si nécessaire.
     *
     * @throws RuntimeException Si la connexion échoue.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Crée et configure la connexion PDO.
     *
     * @return PDO La connexion PDO configurée.
     *
     * @throws RuntimeException Si la connexion échoue.
     */
    private static function createConnection(): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            config('db.host'),
            config('db.port'),
            config('db.name'),
        );

        try {
            $pdo = new PDO(
                dsn: $dsn,
                username: config('db.user'),
                password: config('db.password'),
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ],
            );
        } catch (PDOException $e) {
            throw new RuntimeException(
                message: 'Impossible de se connecter à la base de données : ' . $e->getMessage(),
                previous: $e,
            );
        }

        return $pdo;
    }
}
