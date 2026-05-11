<?php

declare(strict_types=1);

namespace App\Entities;

use DateTimeImmutable;

/**
 * Entité représentant un livre de la bibliothèque d'un utilisateur.
 */
final class BookEntity
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly string $title,
        public readonly string $author,
        public readonly string $description,
        public readonly ?string $photo,
        public readonly bool $isAvailable,
        public readonly DateTimeImmutable $createdAt,
    ) {}
}
