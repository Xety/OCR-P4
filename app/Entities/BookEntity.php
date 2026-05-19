<?php

declare(strict_types=1);

namespace App\Entities;

use App\Core\ORM\AbstractEntity;
use DateTimeImmutable;

/**
 * Entité représentant un livre de la bibliothèque d'un utilisateur.
 */
final class BookEntity extends AbstractEntity
{
    protected static array $relationships = [
        'creator',
    ];

    private ?UserEntity $creator = null;
    private int $userId = 0;
    private string $title = '';
    private string $author = '';
    private string $description = '';
    private ?string $photo = null;
    private bool $isAvailable = true;
    private ?DateTimeImmutable $createdAt = null;

    public function getCreator(): ?UserEntity
    {
        return $this->creator;
    }

    public function setCreator(?UserEntity $creator): void
    {
        $this->creator = $creator;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    public function getIsAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): void
    {
        $this->isAvailable = $isAvailable;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
