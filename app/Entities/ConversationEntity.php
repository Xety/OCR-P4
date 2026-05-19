<?php

declare(strict_types=1);

namespace App\Entities;

use App\Core\ORM\AbstractEntity;
use DateTimeImmutable;

final class ConversationEntity extends AbstractEntity
{
    protected static array $relationships = ['otherUser', 'lastMessage'];

    private ?int $creatorId = null;

    private ?int $receiverId = null;

    private ?DateTimeImmutable $createdAt = null;

    private ?UserEntity $otherUser = null;

    private ?ConversationMessageEntity $lastMessage = null;

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }

    public function setCreatorId(?int $creatorId): void
    {
        $this->creatorId = $creatorId;
    }

    public function getReceiverId(): ?int
    {
        return $this->receiverId;
    }

    public function setReceiverId(?int $receiverId): void
    {
        $this->receiverId = $receiverId;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getOtherUser(): ?UserEntity
    {
        return $this->otherUser;
    }

    public function setOtherUser(?UserEntity $otherUser): void
    {
        $this->otherUser = $otherUser;
    }

    public function getLastMessage(): ?ConversationMessageEntity
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?ConversationMessageEntity $lastMessage): void
    {
        $this->lastMessage = $lastMessage;
    }
}
