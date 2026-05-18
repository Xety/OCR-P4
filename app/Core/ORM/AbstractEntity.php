<?php

declare(strict_types=1);

namespace App\Core\ORM;

use DateTimeImmutable;
use DateTimeInterface;
use ReflectionClass;
use ReflectionNamedType;

/**
 * Classe de base pour toutes les entités persistées.
 *
 * Caractéristiques :
 *  - construction simple via tableau : `new BookEntity(['title' => '...', 'author' => '...'])`
 *
 * Responsabilités (côté « données pures », sans SQL ni PDO) :
 *  - exposer les métadonnées de mapping (table + colonnes) par convention
 *  - hydrater une instance depuis une ligne BDD (`fromRow`)
 *  - extraire une instance vers un tableau prêt pour PDO (`toRow`)
 *  - gérer les conversions de types (scalaires, bool PostgreSQL, dates)
 *
 * Conventions de mapping (aucune annotation requise) :
 *  - table : `XxxEntity` → snake_case + `s` (ex. `UserEntity` → `users`)
 *  - colonne : propriété `camelCase` → `snake_case` (ex. `createdAt` → `created_at`)
 *  - identifiant : propriété `id` (0/null = nouvelle entité, sinon existante)
 *
 * Conventions d'accès :
 *  - hydratation via setters publics : `set<Property>(...)`
 *  - lecture via getters publics : `get<Property>()`
 */
abstract class AbstractEntity
{
    /**
     * Identifiant primaire (0 = entité non persistée).
     */
    private int $id = 0;

    /**
     * Propriétés exclues par défaut de la lecture (`fromRow`) et de l'écriture (`toRow`).
     *
     * Surcharger dans les entités enfants pour protéger des champs sensibles :
     * `protected static array $hidden = ['password'];`
     *
     * @var array Noms de propriétés (camelCase) à masquer par défaut.
     */
    protected static array $hidden = [];

    /**
     * Cache des métadonnées calculées par réflexion, indexé par classe.
     *
     * @var array<class-string, array{table: string, columns: array<string, string>}>
     */
    private static array $metadataCache = [];

    /**
     * @param array $data Valeurs initiales [propriété => valeur].
     */
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * Getter pour l'id.
     *
     * @return int L'identifiant de l'entité (0 si non persistée).
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Setter pour l'id.
      *
     * @param int $id L'identifiant de l'entité.
      *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Hydrate les propriétés via les setters publics correspondants.
     *
     * Les clés inconnues (sans setter) sont ignorées silencieusement.
     *
     * @param array $data [propriété => valeur] à hydrater.
     *
     * @return void
     */
    public function fill(array $data): void
    {
        foreach ($data as $property => $value) {
            $setter = 'set' . ucfirst((string) $property);
            if (method_exists($this, $setter)) {
                $this->{$setter}($value);
            }
        }
    }

    /**
     * Métadonnées de mapping pour l'entité courante.
     * Résultat mis en cache statiquement par classe pour éviter les recalculs coûteux de réflexion.
     * Le tableau retourné contient :
     * - `table` : le nom de la table SQL correspondante à l'entité (ex. `users`)
     * - `columns` : un tableau associatif [propriété => colonne] pour chaque propriété d'instance (ex. `createdAt` => `created_at`)
     *
     * @return array{table: string, columns: array<string, string>} ['table' => string, 'columns' => [propriété => colonne snake_case]]
     */
    public static function metadata(): array
    {
        if (isset(self::$metadataCache[static::class])) {
            return self::$metadataCache[static::class];
        }

        $reflection = new ReflectionClass(static::class);
        $columns = [];
        foreach (self::collectProperties($reflection) as $name) {
            $columns[$name] = self::toSnakeCase($name);
        }

        return self::$metadataCache[static::class] = [
            'table'   => self::resolveTableName(static::class),
            'columns' => $columns,
        ];
    }

    /**
     * Construit une instance hydratée à partir d'une ligne SQL (clé = colonne snake_case).
     *
     * @param array $row [ colonne snake_case => valeur ] à partir d'une ligne SQL
     * @param bool $withHidden Si true, inclut les champs cachés (ex. password) même s'ils sont dans `$hidden`.
     *
     * @return static L'entité hydratée à partir de la ligne SQL.
      *
     */
    public static function fromRow(array $row, bool $withHidden = false): static
    {
        // Utilise la réflexion pour créer une instance sans appeler le constructeur (évite les effets de bord du constructeur lors de l'hydratation).
        $reflection = new ReflectionClass(static::class);
        $entity = $reflection->newInstanceWithoutConstructor();

        $meta = static::metadata();
        foreach ($meta['columns'] as $property => $column) {
            if (! $withHidden && in_array($property, static::$hidden, true)) {
                continue;
            }
            if (! array_key_exists($column, $row)) {
                continue;
            }
            $setter = 'set'.ucfirst($property);
            if (! $reflection->hasMethod($setter)) {
                continue;
            }
            $entity->{$setter}(self::convertIncoming($reflection, $property, $row[$column]));
        }

        return $entity;
    }

    /**
     * Exporte l'entité vers un tableau prêt pour PDO (clé = colonne snake_case).
     *
     * @param bool $withHidden Si true, inclut les champs cachés (ex. password) même s'ils sont dans `$hidden`.
     *
     * @return array [ colonne snake_case => valeur ] à partir des propriétés de l'entité
     */
    public function toRow(bool $withHidden = false): array
    {
        $row = [];
        foreach (static::metadata()['columns'] as $property => $column) {
            if (! $withHidden && in_array($property, static::$hidden, true)) {
                continue;
            }
            $row[$column] = self::normalizeOutgoing($this->readProperty($property));
        }

        return $row;
    }

    /**
     * Normalise une valeur PHP pour l'envoyer à PDO (DateTime → ISO, bool → 't'/'f').
     */
    public static function normalizeOutgoing(mixed $value): mixed
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_bool($value)) {
            return $value ? 't' : 'f';
        }

        return $value;
    }

    /**
     * Lit la valeur d'une propriété via son getter (`getX`).
     * Si aucun getter n'existe pour la propriété, retourne null.
     *
     * @param string $property Le nom de la propriété.
     *
     * @return mixed La valeur de la propriété.
     */
    private function readProperty(string $property): mixed
    {
        $getter = 'get'. ucfirst($property);
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        }

        return null;
    }

    /**
     * Convertit une valeur brute issue de PDO vers le type PHP attendu par la propriété.
     *
     * @param ReflectionClass $reflection La classe contenant la propriété.
     * @param string $property Le nom de la propriété.
     * @param mixed $value La valeur brute issue de PDO.
     *
     * @return mixed La valeur convertie.
     */
    private static function convertIncoming(ReflectionClass $reflection, string $property, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }
        // Si la classe n'a pas la propriété, ou si le getter n'est pas typé, on retourne la valeur brute (ex. pour les propriétés dynamiques ou sans type).
        if (! $reflection->hasProperty($property)) {
            return $value;
        }
        // On récupère le type déclaré du getter correspondant à la propriété, pour faire la conversion appropriée (ex. string → DateTime, string → bool).
        $type = $reflection->getProperty($property)->getType();
        if (! $type instanceof ReflectionNamedType) {
            return $value;
        }
        $name = $type->getName();

        // Support natif de DateTime : si le type attendu est une classe de date, on tente la conversion (ex. `createdAt` typé `DateTimeInterface` et valeur `'2024-01-01 12:00:00'` → objet `DateTimeImmutable`).
        if (is_a($name, DateTimeInterface::class, true)) {
            return new DateTimeImmutable((string) $value);
        }

        // Support natif de booléens PostgreSQL : si le type attendu est bool, on convertit les valeurs courantes de PDO pour les booléens.
        return match ($name) {
            'bool' => $value === true || $value === 't' || $value === 1 || $value === '1' || $value === 'true',
            'int' => (int) $value,
            'float' => (float) $value,
            'string' => (string) $value,
            default => $value,
        };
    }

    /**
     * Collecte les propriétés d'instance (y compris privées héritées), sans doublon.
     *
     * @param ReflectionClass $reflection La classe à analyser.
     *
     * @return array Liste des noms de propriétés d'instance déclarées dans la classe et ses parents (sans les statiques).
     */
    private static function collectProperties(ReflectionClass $reflection): array
    {
        $names = [];
        $current = $reflection;

        while ($current !== false) {
            foreach ($current->getProperties() as $property) {
                // Ignore les propriétés statiques et héritées d'une classe parente
                if ($property->isStatic()) {
                    continue;
                }
                // Seules les propriétés déclarées dans la classe courante sont prises en compte, pour éviter les doublons d'héritage
                if ($property->getDeclaringClass()->getName() !== $current->getName()) {
                    continue;
                }
                $names[] = $property->getName();
            }
            // Passe à la classe parente pour collecter ses propriétés (si elle existe)
            $current = $current->getParentClass();
        }

        return array_values(array_unique($names));
    }

    /**
     * Résout le nom de table : `App\Entities\BookEntity` → `books`.
      *
      * @param string $class Le nom complet de la classe d'entité.
      *
      * @return string Le nom de la table correspondante.
     */
    private static function resolveTableName(string $class): string
    {
        // Extrait le nom de classe sans namespace
        $short = substr($class, (int) strrpos($class, '\\') + 1);
        // Retire le suffixe "Entity"
        $short = preg_replace('/Entity$/', '', $short) ?? $short;

        // Convertit en snake_case et ajoute un "s" pour le pluriel
        return self::toSnakeCase($short).'s';
    }

    /**
     * Convertit `camelCase` ou `PascalCase` en `snake_case`.
     * Explication de la regex : `/(?<!^)[A-Z]/` correspond à toute lettre majuscule qui n'est pas au début de la chaîne.
     * `_$0` signifie qu'on remplace cette majuscule par un underscore suivi de la majuscule elle-même (ex. `ItemMovements` → `item_movements`).
     *
     * @param string $value La chaîne en camelCase ou PascalCase à convertir.
     *
     * @return string La chaîne convertie en snake_case.
     */
    private static function toSnakeCase(string $value): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }
}
