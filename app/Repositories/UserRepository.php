<?php

declare(strict_types=1);

namespace App\Repositories;

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
     * Crée un nouvel utilisateur et retourne son entité.
     *
     * @param string $name Pseudo de l'utilisateur
     * @param string $email Adresse email
     * @param string $password Mot de passe en clair (sera haché ici)
     *
     * @return UserEntity L'entité de l'utilisateur créé
     */
    public function create(string $name, string $email, string $password): UserEntity
    {
        $id = $this->execute(
            sql: 'INSERT INTO users (name, email, password) VALUES (:name, :email, :password) RETURNING id',
            bindings: [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
            ],
        );

        return $this->find((int) $id);
    }

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
     * Met à jour le profil d'un utilisateur.
     *
     * @param int $id Identifiant de l'utilisateur
     * @param string $name Nouveau pseudo
     * @param string $email Nouvel email
     * @param string|null $password Nouveau mot de passe en clair, ou null pour ne pas le modifier
     */
    public function updateProfile(int $id, string $name, string $email, ?string $password = null): void
    {
        if ($password !== null) {
            $this->execute(
                sql: 'UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id',
                bindings: [
                    'name'     => $name,
                    'email'    => $email,
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'id'       => $id,
                ],
            );
        } else {
            $this->execute(
                sql: 'UPDATE users SET name = :name, email = :email WHERE id = :id',
                bindings: ['name' => $name, 'email' => $email, 'id' => $id],
            );
        }
    }

    /**
     * Hydrate une UserEntity depuis une ligne PDO.
     *
     * @param array $row Une ligne de résultat PDO associatif contenant les champs d'un utilisateur.
     *
     * @return UserEntity L'entité utilisateur correspondante à la ligne de données.
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
