<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\BookEntity;
use App\Core\ORM\AbstractRepository;

/**
 * Repository pour la ressource Book.
 *
 * Hérite des opérations génériques de {@see AbstractRepository}
 * et n'expose que les requêtes spécifiques au domaine livre.
 *
 * @extends AbstractRepository<BookEntity>
 */
final class BookRepository extends AbstractRepository
{
    /**
     * Retourne tous les livres d'un utilisateur, triés par date de création décroissante.
     *
     * @return array<BookEntity>
     */
    public function findByUserId(int $userId): array
    {
        return $this->findBy(['userId' => $userId], ['createdAt' => 'DESC']);
    }

    /**
     * Retourne une page de livres d'un utilisateur.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     * @param int $limit Le nombre maximum de livres à retourner.
     * @param int $offset Le nombre de livres à ignorer (pour la pagination).
     *
     * @return array<BookEntity>
     */
    public function findByUserIdPaginated(int $userId, int $limit, int $offset): array
    {
        return $this->findBy(['userId' => $userId], ['createdAt' => 'DESC'], $limit, $offset);
    }

    /**
     * Retourne le nombre total de livres d'un utilisateur.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     *
     * @return int Le nombre total de livres de l'utilisateur.
     */
    public function countByUserId(int $userId): int
    {
        return $this->countBy(['userId' => $userId]);
    }
}
