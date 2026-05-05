<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Contracts\RuleInterface;

/**
 * Vérifie que la valeur est une adresse email valide.
 */
final class Email implements RuleInterface
{
    /**
     * Vérifie que la valeur du champ est une chaîne de caractères et correspond à un format d'email valide.
     *
     * @param string $field Le nom du champ (ex: 'email')
     * @param mixed $value La valeur soumise pour ce champ
     * @param array $data Toutes les données du formulaire (ex: $request->body), pas nécessaire pour cette règle mais respect du contrat
     * @return bool True si la valeur est une adresse email valide, false sinon.
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
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
        return "Le champ {$field} doit être une adresse email valide.";
    }
}
