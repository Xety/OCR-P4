<?php

declare(strict_types=1);

namespace App\Entities;

use App\Core\ORM\AbstractEntity;
use DateTimeImmutable;

final class ConversationMessageEntity extends AbstractEntity
{
    /**
     * Propriétés hydratées manuellement via JOIN — non persistées en base.
     */
    protected static array $relationships = ['sender'];

    private int $conversationId = 0;

    private ?int $senderId = null;

    private string $content = '';

    private ?DateTimeImmutable $createdAt = null;

    private ?UserEntity $sender = null;

    public function getConversationId(): int
    {
        return $this->conversationId;
    }

    public function setConversationId(int $conversationId): void
    {
        $this->conversationId = $conversationId;
    }

    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    public function setSenderId(?int $senderId): void
    {
        $this->senderId = $senderId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getSender(): ?UserEntity
    {
        return $this->sender;
    }

    public function setSender(?UserEntity $sender): void
    {
        $this->sender = $sender;
    }
}
