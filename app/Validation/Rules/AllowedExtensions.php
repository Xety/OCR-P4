<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Contracts\RuleInterface;

/**
 * Vérifie que le fichier uploadé a une extension autorisée.
 */
final class AllowedExtensions implements RuleInterface
{
    /**
     * @param array $extensions Liste des extensions autorisées (ex: ['jpg', 'png'])
     */
    public function __construct(private readonly array $extensions) {}

    /**
     * Vérifie que la valeur du champ est un fichier uploadé avec une extension autorisée.
     *
     * @param string $field Le nom du champ (ex: 'photo')
     * @param mixed $value La valeur soumise pour ce champ (doit être un tableau de type $_FILES)
      * @param array $data Toutes les données du formulaire (ex: $request->body)

      * @return bool True si la valeur est un fichier uploadé avec une extension autorisée, false sinon.
      */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if (! is_array($value) || ($value['error'] ?? -1) !== UPLOAD_ERR_OK) {
            return true;
        }
        $ext = strtolower(pathinfo($value['name'] ?? '', PATHINFO_EXTENSION));
        return in_array($ext, $this->extensions, strict: true);
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
        return 'Format non autorisé (' . implode(', ', $this->extensions) . ').';
    }
}