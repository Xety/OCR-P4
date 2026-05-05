<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Contracts\RuleInterface;

/**
 * Vérifie que le champ n'est pas vide après suppression des espaces.
 */
final class Required implements RuleInterface
{
    /**
     * Vérifie que la valeur du champ est une chaîne non vide après suppression des espaces.
     *
     * @param string $field Le nom du champ (ex: 'email')
     * @param mixed $value La valeur soumise pour ce champ
     * @param array $data Toutes les données du formulaire (ex: $request->body), pas nécessaire pour cette règle mais respect du contrat
     * @return bool True si la valeur est une chaîne non vide après suppression des espaces, false sinon.
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        return is_string($value) && trim($value) !== '';
    }

    /**
     * Retourne le message d'erreur si la validation échoue.
     *
     * @param string $field Le nom du champ (ex: 'email')
     *
     * @return string Message d'erreur à afficher si la validation échoue.
     */
    public function message(string $field): string
    {
        return "Le champ {$field} est obligatoire.";
    }
}
