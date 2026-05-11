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
}
