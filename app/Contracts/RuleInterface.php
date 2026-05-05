<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Contrat pour une règle de validation.
 */
interface RuleInterface
{
    /**
     * Vérifie si la valeur est valide.
     *
     * @param string $field Le nom du champ (ex: 'email')
     * @param mixed $value La valeur soumise pour ce champ
     * @param array $data Toutes les données du formulaire (utile pour Confirmed)
     *
     * @return bool True si la valeur est valide selon la règle, false sinon.
     */
    public function passes(string $field, mixed $value, array $data): bool;

    /**
     * Retourne le message d'erreur si la règle échoue.
     *
     * @param string $field Le nom du champ (ex: 'email')
     *
     * @return string Message d'erreur à afficher si la validation échoue.
     */
    public function message(string $field): string;
}
