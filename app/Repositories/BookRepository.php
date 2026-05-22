<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\BookEntity;
use App\Entities\UserEntity;
use App\Core\ORM\AbstractRepository;
use PDO;

/**
 * Repository pour la ressource Book.
 *
 * Hérite des opérations génériques de {@see AbstractRepository}
 * et n'expose que les requêtes spécifiques au domaine livre.
 *
 * @extends AbstractRepository<BookEntity>
 */
final class BookRepository extends AbstractRepository
{
    /**
     * Retourne tous les livres d'un utilisateur, triés par date de création décroissante.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     *
     * @return array<BookEntity>
     */
    public function findByUserId(int $userId): array
    {
        return $this->findBy(['userId' => $userId], ['createdAt' => 'DESC']);
    }

    /**
     * Retourne une page de tous les livres avec le nom du vendeur,
     * filtrés optionnellement par titre ou auteur.
     *
     * @param string $search Terme de recherche à appliquer sur le titre ou l'auteur (optionnel).
     *
     * @return array<BookEntity>
     */
    public function findAllWithSearch(string $search = ''): array
    {
        $sql = 'SELECT b.*, u.name AS user_name FROM books b INNER JOIN users u ON b.user_id = u.id';

        $bindings = [];
        if ($search !== '') {
            $sql .= ' WHERE (b.title ILIKE :search OR b.author ILIKE :search)';
            $bindings['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY b.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return array_map(
            function (array $row): BookEntity {
                $book = BookEntity::fromRow($row);
                $book->setCreator(new UserEntity(['id' => (int) $row['user_id'], 'name' => $row['user_name']]));

                return $book;
            },
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }

    /**
     * Retourne un livre par son identifiant, avec le nom du vendeur.
     *
     * @param int $id L'identifiant du livre.
     *
     * @return BookEntity|null Le livre trouvé, ou null s'il n'existe pas.
     */
    public function findById(int $id): ?BookEntity
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.*, u.name AS user_name, u.avatar AS user_avatar FROM books b INNER JOIN users u ON b.user_id = u.id WHERE b.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        $book = BookEntity::fromRow($row);
        $book->setCreator(new UserEntity(['id' => (int) $row['user_id'], 'name' => $row['user_name'], 'avatar' => $row['user_avatar']]));

        return $book;
    }

    /**
     * Retourne les derniers livres ajoutés, avec le nom du vendeur.
     *
     * @param int $limit Le nombre maximum de livres à retourner.
     *
     * @return array<BookEntity>
     */
    public function findLatest(int $limit = 4): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.*, u.name AS user_name
            FROM books b
            INNER JOIN users u ON b.user_id = u.id
            ORDER BY b.created_at DESC
            LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            function (array $row): BookEntity {
                $book = BookEntity::fromRow($row);
                $book->setCreator(new UserEntity(['id' => (int) $row['user_id'], 'name' => $row['user_name']]));

                return $book;
            },
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }
}
