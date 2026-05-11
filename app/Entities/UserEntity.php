<?php

declare(strict_types=1);

namespace App\Entities;

use App\Core\ORM\AbstractEntity;
use DateTimeImmutable;

/**
 * Entité représentant un utilisateur.
 */
final class UserEntity extends AbstractEntity
{
    protected static array $hidden = [
        'password'
    ];

    private string $name = '';
    private string $email = '';
    private ?DateTimeImmutable $createdAt = null;
    private ?string $password = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }
}
