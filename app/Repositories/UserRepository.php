<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\UserEntity;
use DateTimeImmutable;

/**
 * Repository pour la ressource User.
 *
 * Responsabilité unique : accès aux données utilisateur en base.
 * Retourne des UserEntity — objets de données purs, sans dépendance PDO.
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
        $row = $this->selectOne(
            sql: 'SELECT id, name, email, password, created_at FROM users WHERE email = :email',
            bindings: ['email' => $email],
        );

        if ($row === null) {
            return null;
        }

        return new UserEntity(
            id: (int) $row['id'],
            name: (string) $row['name'],
            email: (string) $row['email'],
            createdAt: new DateTimeImmutable((string) $row['created_at']),
            // On hydrate aussi le hash de mot de passe pour la vérification lors de la connexion.
            password: (string) $row['password'],
        );
    }
    /**
     * Retourne tous les utilisateurs triés par identifiant.
     *
     * @return array<int, UserEntity>
     */
    public function all(): array
    {
        $rows = $this->select('SELECT id, name, email, created_at FROM users ORDER BY id ASC');

        return array_map(fn(array $row) => $this->hydrate($row), $rows);
    }

    /**
     * Retourne un utilisateur par son identifiant, ou null si introuvable.
     */
    public function find(int $id): ?UserEntity
    {
        $row = $this->selectOne(
            sql: 'SELECT id, name, email, created_at FROM users WHERE id = :id',
            bindings: ['id' => $id],
        );

        return $row !== null ? $this->hydrate($row) : null;
    }

    /**
     * Hydrate une UserEntity depuis une ligne PDO.
     *
     * @param array<string, mixed> $row
     */
    private function hydrate(array $row): UserEntity
    {
        return new UserEntity(
            id: (int) $row['id'],
            name: (string) $row['name'],
            email: (string) $row['email'],
            createdAt: new DateTimeImmutable((string) $row['created_at']),
        );
    }
}
