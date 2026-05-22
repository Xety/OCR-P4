<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\ORM\AbstractRepository;
use App\Entities\ConversationMessageEntity;
use App\Entities\UserEntity;
use PDO;

final class ConversationMessageRepository extends AbstractRepository
{
    /**
     * Retourne tous les messages d'une conversation, ordre chronologique,
     * avec le nom de l'expéditeur.
     *
     * @param int $conversationId L'ID de la conversation
     *
     * @return array<ConversationMessageEntity>
     */
    public function findByConversationId(int $conversationId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT cm.*, u.name AS sender_name, u.avatar AS sender_avatar
             FROM conversation_messages cm
             LEFT JOIN users u ON cm.sender_id = u.id
             WHERE cm.conversation_id = :conversation_id
             ORDER BY cm.created_at ASC'
        );
        $stmt->execute(['conversation_id' => $conversationId]);

        return array_map(function (array $row): ConversationMessageEntity {
            $message = ConversationMessageEntity::fromRow($row);

            // Créer une entité UserEntity pour l'expéditeur — sentinelle si l'utilisateur a été supprimé
            $sender = $row['sender_id'] !== null
                ? new UserEntity(['id' => (int) $row['sender_id'], 'name' => $row['sender_name'], 'avatar' => $row['sender_avatar']])
                : UserEntity::deleted();
            $message->setSender($sender);

            return $message;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Insère un nouveau message.
     *
     * @param ConversationMessageEntity $message Le message à créer
     *
      * @return ConversationMessageEntity Le message créé avec son ID et createdAt mis à jour
     */
    public function create(ConversationMessageEntity $message): ConversationMessageEntity
    {
        return $this->save($message);
    }
}
