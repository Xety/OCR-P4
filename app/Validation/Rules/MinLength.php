<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Contracts\RuleInterface;

/**
 * Vérifie que la longueur de la valeur est supérieure ou égale au minimum.
 */
final class MinLength implements RuleInterface
{
    /**
     * @param int $min Le nombre minimum de caractères requis
     */
    public function __construct(private readonly int $min) {}

    /**
     * Vérifie que la valeur du champ est une chaîne de caractères et que sa longueur est supérieure ou égale au minimum requis.
     *
     * @param string $field Le nom du champ (ex: 'password')
     * @param mixed $value La valeur soumise pour ce champ
     * @param array $data Toutes les données du formulaire (ex: $request->body), pas nécessaire pour cette règle mais respect du contrat
     * @return bool True si la valeur est une chaîne de caractères et que sa longueur est supérieure ou égale au minimum requis, false sinon.
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        return is_string($value) && strlen($value) >= $this->min;
    }

    /**
     * Retourne le message d'erreur si la validation échoue.
     *
     * @param string $field Le nom du champ (ex: 'password')
     *
     * @return string Message d'erreur à afficher si la validation échoue.
     */
    public function message(string $field): string
    {
        return "Le champ {$field} doit contenir au moins {$this->min} caractères.";
    }
}
