<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

/**
 * Classe de base abstraite pour tous les repositories.
 *
 * Reçoit la connexion PDO par injection de constructeur (principe D de SOLID)
 * et expose des helpers protégés pour exécuter des requêtes SQL.
 */
abstract class AbstractRepository
{
    public function __construct(protected readonly PDO $db) {}

    /**
     * Exécute une requête SQL et retourne tous les résultats.
     *
     * @param string $sql La requête SQL à exécuter.
     * @param array $bindings Les valeurs à lier aux paramètres de la requête.
     *
     * @return array Un tableau de résultats, chaque résultat étant un tableau associatif.
     */
    protected function select(string $sql, array $bindings = []): array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($bindings);

        return $statement->fetchAll();
    }

    /**
     * Exécute une requête SQL et retourne la première ligne, ou null.
     *
     * @param string $sql La requête SQL à exécuter.
     * @param array $bindings Les valeurs à lier aux paramètres de la requête.
     *
     * @return array|null La première ligne du résultat, ou null si aucun résultat.
     */
    protected function selectOne(string $sql, array $bindings = []): ?array
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($bindings);

        $row = $statement->fetch();

        return $row !== false ? $row : null;
    }

    /**
     * Exécute une requête d'écriture (INSERT, UPDATE, DELETE).
     * Supporte la clause RETURNING de PostgreSQL pour récupérer l'identifiant inséré.
     *
     * @param string $sql La requête SQL à exécuter.
     * @param array $bindings Les valeurs à lier aux paramètres de la requête.
     *
     * @return int|null L'identifiant de la ligne affectée, ou null si non applicable.
     */
    protected function execute(string $sql, array $bindings = []): ?int
    {
        $statement = $this->db->prepare($sql);
        $statement->execute($bindings);

        $row = $statement->fetch();

        return $row !== false ? (int) array_values($row)[0] : null;
    }
}
