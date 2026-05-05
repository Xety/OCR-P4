<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Contracts\RuleInterface;

/**
 * Vérifie que la valeur du champ correspond à celle d'un autre champ.
 */
final class Confirmed implements RuleInterface
{
    /**
     * @param string $otherField Le nom du champ de confirmation (ex: 'password_confirmation')
     */
    public function __construct(private readonly string $otherField) {}

    /**
     *  Vérifie que la valeur du champ correspond à celle du champ de confirmation.
     *
     * @param string $field Le nom du champ à valider (ex: 'password')
     * @param mixed $value La valeur soumise pour ce champ
     * @param array $data Toutes les données du formulaire (ex: $request->body), nécessaire pour accéder au champ de confirmation
     * @return bool True si la valeur correspond à celle du champ de confirmation, false sinon.
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        return $value === ($data[$this->otherField] ?? null);
    }

    /**
     * Retourne le message d'erreur si la validation échoue.
     *
     * @param string $field Le nom du champ à valider (ex: 'password')
     *
     * @return string Message d'erreur à afficher si la validation échoue.
     */
    public function message(string $field): string
    {
        return "Le champ {$field} ne correspond pas à la confirmation.";
    }
}
