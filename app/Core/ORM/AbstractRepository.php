<?php

declare(strict_types=1);

namespace App\Core\ORM;

use InvalidArgumentException;
use PDO;
use RuntimeException;

/**
 * Repository abstrait inspiré de Doctrine ORM.
 *
 * Responsabilité unique : **interaction avec la base de données**
 * (construction du SQL, préparation/exécution des requêtes via PDO).
 *
 * Toute la logique de mapping / hydratation / conversion de types
 * vit dans AbstractEntity :
 *  - `Entity::metadata()` → table + colonnes
 *  - `Entity::fromRow($row)` → instance hydratée depuis une ligne SQL
 *  - `$entity->toRow()` → tableau prêt pour PDO
 *
 * Persistance :
 *  - `save()` : INSERT si `id === 0`, sinon UPDATE
 *  - `delete()` : suppression par `id`
 *
 */
abstract class AbstractRepository
{
    public function __construct(protected readonly PDO $pdo) {}

    /**
     * Recherche une entité par son identifiant.
     *
     * @return AbstractEntity|null
     */
    public function find(int $id): ?AbstractEntity
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Recherche des entités selon des critères d'égalité.
     *
     * @param array $criteria ['property' => $value]
     * @param array|null $orderBy  ['property' => 'ASC'|'DESC']
     * @param int|null $limit Nombre maximum de résultats à retourner
     * @param int|null $offset Nombre de résultats à ignorer (pour la pagination)
     *
     * @return array<AbstractEntity>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $entityClass = $this->getEntityClass();
        $meta = $entityClass::metadata();
        $sql = 'SELECT * FROM ' . $meta['table'];

        // Construction de la clause WHERE et des bindings à partir des critères
        [$where, $bindings] = $this->buildWhere($criteria, $meta['columns']);

        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }

        // Construction de la clause ORDER BY
        if ($orderBy !== null && $orderBy !== []) {
            $parts = [];
            foreach ($orderBy as $prop => $direction) {
                $column = $meta['columns'][$prop] ?? throw new InvalidArgumentException("Propriété inconnue : {$prop}");
                $dir = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                $parts[] = $column . ' ' . $dir;
            }
            $sql .= ' ORDER BY ' . implode(', ', $parts);
        }

        // Ajout de la pagination
        if ($limit !== null) {
            $sql .= ' LIMIT ' . $limit;
        }
        if ($offset !== null) {
            $sql .= ' OFFSET ' . $offset;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            function (array $row) use ($entityClass): AbstractEntity {
                return $entityClass::fromRow($row);
            },
            $rows,
        );
    }

    /**
     * Recherche une seule entité selon des critères.
     *
     * @param array<string, mixed> $criteria
     *
     * @return AbstractEntity|null
     */
    public function findOneBy(array $criteria): ?AbstractEntity
    {
        $results = $this->findBy($criteria, null, 1);

        return $results[0] ?? null;
    }

    /**
     * Persiste une entité : INSERT si `id === 0`, UPDATE sinon.
     *
     * @param AbstractEntity $entity L'entité à persister (doit être du type géré par ce repository)
     *
     * @return AbstractEntity L'entité avec son `id` renseigné après INSERT.
     */
    public function save(AbstractEntity $entity): AbstractEntity
    {
        $this->assertEntityType($entity);

        if ($entity->getId() === 0) {
            return $this->insert($entity);
        }

        return $this->update($entity);
    }

    /**
     * Supprime une entité de la BDD.
     *
     * @param AbstractEntity $entity L'entité à supprimer (doit avoir un id non nul)
     *
     * @param bool true si la suppression a réussi, false sinon
     */
    public function delete(AbstractEntity $entity): bool
    {
        $this->assertEntityType($entity);

        $meta = $this->getEntityClass()::metadata();
        $stmt = $this->pdo->prepare(sprintf('DELETE FROM %s WHERE id = :id', $meta['table']));

        return $stmt->execute(['id' => $entity->getId()]);
    }

    /**
     * Exécute un INSERT et hydrate l'id généré sur l'entité.
     */
    private function insert(AbstractEntity $entity): AbstractEntity
    {
        $meta = $this->getEntityClass()::metadata();
        $row = $entity->toRow(withHidden: true, skipNull: true);
        unset($row['id']);

        $columns = array_keys($row);
        $placeholders = array_map(fn (string $col): string => ':'.$col, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s) RETURNING id',
            $meta['table'],
            implode(', ', $columns),
            implode(', ', $placeholders),
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($row);

        $entity->setId((int) $stmt->fetchColumn());

        return $entity;
    }

    /**
     * Exécute un UPDATE sur l'entité existante (identifiée par son id).
     *
     * @param AbstractEntity $entity L'entité à mettre à jour.
     *
     * @return AbstractEntity L'entité mise à jour.
     */
    private function update(AbstractEntity $entity): AbstractEntity
    {
        $meta = $this->getEntityClass()::metadata();
        $row = $entity->toRow();
        unset($row['id']);

        $setParts = array_map(fn (string $col): string => $col.' = :'.$col, array_keys($row));

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id = :id',
            $meta['table'],
            implode(', ', $setParts),
        );

        $row['id'] = $entity->getId();

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($row);

        return $entity;
    }

    /**
     * Construit une clause WHERE à partir d'un tableau de critères.
     *
     * @param array $criteria ['property' => $value]
     * @param array $columns ['property' => 'column_name']
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function buildWhere(array $criteria, array $columns): array
    {
        if ($criteria === []) {
            return ['', []];
        }

        $parts = [];
        $bindings = [];
        foreach ($criteria as $property => $value) {
            $column = $columns[$property] ?? throw new InvalidArgumentException("Propriété inconnue : {$property}");
            $parts[] = $column.' = :'.$column;
            $bindings[$column] = AbstractEntity::normalizeOutgoing($value);
        }

        return [implode(' AND ', $parts), $bindings];
    }

    /**
     * Résout la classe d'entité associée par convention :
     * `App\Repositories\BookRepository` → `App\Entities\BookEntity`.
     *
     * @return string<AbstractEntity> Le nom de la classe d'entité gérée par ce repository.
     */
    private function getEntityClass(): string
    {
        // Résolution de la classe d'entité associée par convention
        $entityClass = (string) preg_replace(
            ['/\\\\Repositories\\\\/', '/Repository$/'],
            ['\\Entities\\', 'Entity'],
            static::class,
        );

        // Vérification que la classe d'entité existe et est valide
        if (! class_exists($entityClass) || ! is_subclass_of($entityClass, AbstractEntity::class)) {
            throw new RuntimeException(sprintf(
                'Classe d\'entité introuvable pour le repository %s (attendu : %s).',
                static::class,
                $entityClass,
            ));
        }

        return $entityClass;
    }

    /**
     * Vérifie que l'entité passée correspond bien au type géré par ce repository.
     *
     * @param AbstractEntity $entity L'entité à vérifier.
     */
    private function assertEntityType(AbstractEntity $entity): void
    {
        $entityClass = $this->getEntityClass();
        if (! $entity instanceof $entityClass) {
            throw new InvalidArgumentException(sprintf(
                'Ce repository attend une instance de %s, %s reçu.',
                $entityClass,
                $entity::class,
            ));
        }
    }
}
