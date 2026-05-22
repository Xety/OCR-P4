<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\ORM\AbstractRepository;
use App\Entities\ConversationEntity;
use App\Entities\ConversationMessageEntity;
use App\Entities\UserEntity;
use DateTimeImmutable;
use PDO;

final class ConversationRepository extends AbstractRepository
{
    /**
     * Retourne toutes les conversations d'un utilisateur,
     * avec le nom de l'autre participant et le dernier message.
     *
     * @return ConversationEntity[]
     */
    public function findAllForUser(int $userId): array
    {
        $sql = <<<SQL
            SELECT
                c.id, c.creator_id, c.receiver_id, c.created_at,
                u.id   AS other_user_id,
                u.name AS other_user_name,
                u.avatar AS other_user_avatar,
                /* Sous-requêtes pour récupérer le dernier message et sa date — optimisé pour éviter les N+1 */
                (SELECT content    FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) AS last_message,
                (SELECT created_at FROM conversation_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) AS last_message_at
            FROM conversations c
            /* JOIN sur l'autre participant — LEFT pour garder la conversation si l'utilisateur a été supprimé */
            LEFT JOIN users u ON u.id = CASE WHEN c.creator_id = :uid THEN c.receiver_id ELSE c.creator_id END
            WHERE c.creator_id = :uid OR c.receiver_id = :uid
            ORDER BY last_message_at DESC NULLS LAST, c.created_at DESC
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(function (array $row): ConversationEntity {
            $conversation = ConversationEntity::fromRow($row);

            // Créer une entité UserEntity pour l'autre participant — sentinelle si l'utilisateur a été supprimé
            $otherUser = $row['other_user_id'] !== null
                ? new UserEntity(['id' => (int) $row['other_user_id'], 'name' => $row['other_user_name'], 'avatar' => $row['other_user_avatar']])
                : UserEntity::deleted();
            $conversation->setOtherUser($otherUser);

            // Créer une entité ConversationMessageEntity pour le dernier message (si existant)
            if ($row['last_message'] !== null) {
                $lastMessage = new ConversationMessageEntity(['content' => $row['last_message'], 'createdAt' => $row['last_message_at'] !== null ? new DateTimeImmutable($row['last_message_at']) : null]);
                $conversation->setLastMessage($lastMessage);
            }

            return $conversation;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Trouve ou crée une conversation entre deux utilisateurs.
     *
     * @param int $creator L'ID de l'utilisateur créateur de la conversation
     * @param int $receiver L'ID de l'autre utilisateur de la conversation
     *
     * @return ConversationEntity La conversation existante ou nouvellement créée entre les deux utilisateurs
     */
    public function findOrCreateBetween(int $creator, int $receiver): ConversationEntity
    {
        // Vérification dans les deux sens : C→R ou R→C
        $sql = <<<SQL
            SELECT * FROM conversations
            WHERE (creator_id = :c AND receiver_id = :r) OR (creator_id = :r AND receiver_id = :c)
            LIMIT 1
        SQL;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':c' => $creator, ':r' => $receiver]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row !== false) {
            return ConversationEntity::fromRow($row);
        }

        $conversation = new ConversationEntity(['creatorId' => $creator, 'receiverId' => $receiver]);

        return $this->save($conversation);
    }
}
