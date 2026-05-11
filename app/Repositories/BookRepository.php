<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\BookEntity;
use DateTimeImmutable;

/**
 * Repository pour la ressource Book.
 *
 * Responsabilité unique : accès aux données livre en base.
 * Retourne des BookEntity.
 */
final class BookRepository extends AbstractRepository
{
    /**
     * Retourne tous les livres d'un utilisateur triés par date de création décroissante.
     *
     * @param int $userId L'ID de l'utilisateur dont on veut récupérer les livres.
     *
     * @return array<BookEntity>
     */
    public function findByUserId(int $userId): array
    {
        $rows = $this->select(
            sql: 'SELECT id, user_id, title, author, description, photo, is_available, created_at
                  FROM books
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC',
            bindings: ['user_id' => $userId],
        );

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    /**
     * Hydrate une BookEntity depuis une ligne PDO.
     *
     * @param array $row Une ligne de résultat PDO associatif contenant les champs d'un livre.
     *
     * @return BookEntity L'entité livre correspondante à la ligne de données.
     */
    private function hydrate(array $row): BookEntity
    {
        return new BookEntity(
            id: (int) $row['id'],
            userId: (int) $row['user_id'],
            title: (string) $row['title'],
            author: (string) $row['author'],
            description: (string) $row['description'],
            photo: isset($row['photo']) && $row['photo'] !== '' ? (string) $row['photo'] : null,
            // PostgreSQL retourne 't' / 'f' pour les booléens via PDO
            isAvailable: $row['is_available'] === 't' || $row['is_available'] === true,
            createdAt: new DateTimeImmutable((string) $row['created_at']),
        );
    }
}
