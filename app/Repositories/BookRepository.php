<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\BookEntity;
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
     * @return array<BookEntity>
     */
    public function findByUserId(int $userId): array
    {
        return $this->findBy(['userId' => $userId], ['createdAt' => 'DESC']);
    }

    /**
     * Retourne une page de livres d'un utilisateur.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     * @param int $limit Le nombre maximum de livres à retourner.
     * @param int $offset Le nombre de livres à ignorer (pour la pagination).
     *
     * @return array<BookEntity>
     */
    public function findByUserIdPaginated(int $userId, int $limit, int $offset): array
    {
        return $this->findBy(['userId' => $userId], ['createdAt' => 'DESC'], $limit, $offset);
    }

    /**
     * Retourne le nombre total de livres d'un utilisateur.
     *
     * @param int $userId L'identifiant de l'utilisateur.
     *
     * @return int Le nombre total de livres de l'utilisateur.
     */
    public function countByUserId(int $userId): int
    {
        return $this->countBy(['userId' => $userId]);
    }

    /**
     * Retourne une page de tous les livres avec le nom du vendeur,
     * filtrés optionnellement par titre ou auteur.
     *
     * @return array<BookEntity>
     */
    public function findAllPaginated(int $limit, int $offset, string $search = ''): array
    {
        $sql = 'SELECT b.*, u.name AS user_name FROM books b INNER JOIN users u ON b.user_id = u.id';

        $bindings = [];
        if ($search !== '') {
            $sql .= ' WHERE (b.title ILIKE :search OR b.author ILIKE :search)';
            $bindings['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY b.created_at DESC';

        $stmt = $this->pdo->prepare($sql . ' LIMIT :limit OFFSET :offset');
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            function (array $row): BookEntity {
                return BookEntity::fromRow($row, withHidden: true);
            },
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }

    /**
     * Retourne le nombre total de livres, filtrés optionnellement par titre ou auteur.
     *
     * @param string $search Le terme de recherche pour filtrer les livres.
     *
     * @return int Le nombre total de livres correspondant au filtre.
     */
    public function countAll(string $search = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM books b';

        $bindings = [];
        if ($search !== '') {
            $sql .= ' WHERE (b.title ILIKE :search OR b.author ILIKE :search)';
            $bindings['search'] = '%' . $search . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        return (int) $stmt->fetchColumn();
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
            'SELECT b.*, u.name AS user_name FROM books b INNER JOIN users u ON b.user_id = u.id WHERE b.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return BookEntity::fromRow($row, withHidden: true);
    }
}
