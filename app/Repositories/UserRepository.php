<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\ORM\AbstractRepository;
use App\Entities\UserEntity;
use DateTimeImmutable;

/**
 * Repository pour la ressource User.
 *
 * Responsabilité unique : accès aux données utilisateur en base.
 * Retourne des UserEntity.
 */
final class UserRepository extends AbstractRepository
{
    /**
    * Retourne un utilisateur par son adresse email, ou null si introuvable.
    *
    * @param string $email L'adresse email de l'utilisateur à rechercher.
    *
    * @return UserEntity|null L'entité utilisateur correspondante, ou null si aucune correspondance.
    */
    public function findByEmail(string $email): ?UserEntity
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Retourne un utilisateur avec son mot de passe hydraté (pour l'authentification uniquement).
     *
     * @param string $email L'adresse email de l'utilisateur.
     *
     * @return UserEntity|null L'entité avec le mot de passe renseigné, ou null si introuvable.
     */
    public function findByEmailForAuth(string $email): ?UserEntity
    {
        $meta = UserEntity::metadata();
        $stmt = $this->pdo->prepare(
            sprintf('SELECT * FROM %s WHERE email = :email LIMIT 1', $meta['table'])
        );
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row !== false ? UserEntity::fromRow($row, withHidden: true) : null;
    }

    /**
     * Met à jour uniquement le mot de passe d'un utilisateur.
     *
     * @param int $id L'identifiant de l'utilisateur.
     *
     * @param string $hash Le nouveau hash bcrypt du mot de passe.
     */
    public function updatePassword(int $id, string $hash): void
    {
        $meta = UserEntity::metadata();
        $stmt = $this->pdo->prepare(
            sprintf('UPDATE %s SET password = :password WHERE id = :id', $meta['table'])
        );
        $stmt->execute(['password' => $hash, 'id' => $id]);
    }
}
