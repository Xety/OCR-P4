<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Contracts\RuleInterface;

/**
 * Vérifie que la valeur est un fichier uploadé et ne dépasse pas la taille maximale autorisée.
 */
final class MaxFileSize implements RuleInterface
{

    /**
     * @param int $maxMb Le poids maximum du fichier en mégaoctets
     */
    public function __construct(private readonly int $maxMb) {}

    /**
     * Vérifie que la valeur du champ est un fichier uploadé et ne dépasse pas la taille maximale autorisée.
     *
     * @param string $field Le nom du champ (ex: 'photo')
     * @param mixed $value La valeur soumise pour ce champ
     * @param array $data Toutes les données du formulaire (ex: $request->body)
     *
     * @return bool True si la valeur est un fichier uploadé valide et ne dépasse pas la taille maximale, false sinon.
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        // Pas notre rôle si le code d'erreur n'est pas OK
        if (! is_array($value) || ($value['error'] ?? -1) !== UPLOAD_ERR_OK) {
            return true;
        }
        return ($value['size'] ?? 0) <= $this->maxMb * 1024 * 1024;
    }

    /**
     * Retourne le message d'erreur si la validation échoue.
     *
     * @param string $field Le nom du champ (ex: 'photo')
     *
     * @return string Message d'erreur à afficher si la validation échoue.
     */
    public function message(string $field): string
    {
        return "La photo est trop volumineuse (maximum {$this->maxMb} Mo).";
    }
}
