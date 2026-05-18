<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Contracts\RuleInterface;

/**
 * Vérifie que la valeur est une représentation booléenne valide.
 * Accepte : '0', '1', 'true', 'false', 'on', 'off'.
 */
final class Boolean implements RuleInterface
{
    /**
     * @var array Liste des valeurs acceptées pour représenter un booléen.
     */
    private const ACCEPTED = ['0', '1', 'true', 'false', 'on', 'off'];

    /**
     * Vérifie si la valeur passée est une représentation booléenne valide.
     *
     * @param string $field Le nom du champ (ex: 'is_published')
     * @param mixed $value La valeur à vérifier
     * @param array $data L'ensemble des données du formulaire
     *
     * @return bool True si la valeur est une représentation booléenne valide, false sinon.
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        return in_array($value, self::ACCEPTED, strict: true);
    }

    /**
     * Retourne le message d'erreur si la validation échoue.
     *
     * @param string $field Le nom du champ (ex: 'is_published')
     *
     * @return string Message d'erreur à afficher si la validation échoue.
     */
    public function message(string $field): string
    {
        return "Le champ {$field} doit être une valeur booléenne.";
    }
}