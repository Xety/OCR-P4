<?php

declare(strict_types=1);

namespace App\Entities;

use DateTimeImmutable;

/**
 * Entité représentant un utilisateur.
 */
final class UserEntity
{
    public function __construct(
        public readonly int               $id,
        public readonly string            $name,
        public readonly string            $email,
        public readonly DateTimeImmutable $createdAt,
        public readonly ?string           $password = null,
    ) {}
}
