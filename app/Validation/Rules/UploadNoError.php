<?php

declare(strict_types=1);

namespace App\Validation\Rules;

use App\Contracts\RuleInterface;

/**
 * Validation pour vérifier qu'un fichier uploadé n'a pas rencontré d'erreur lors de l'upload.
 */
final class UploadNoError implements RuleInterface
{
    /**
     * Code d'erreur de l'upload, initialisé à UPLOAD_ERR_OK par défaut.
     *
     * @var int
     */
    private int $errorCode = UPLOAD_ERR_OK;

    /**
     * Vérifie que le champ contient un fichier uploadé sans erreur.
     *
     * @param string $field Le nom du champ (ex: 'photo')
     * @param mixed $value La valeur soumise pour ce champ (doit être un tableau de type $_FILES)
     * @param array $data Toutes les données du formulaire (ex: $request->body), pas nécessaire pour cette règle mais respect du contrat
     *
     * @return bool True si le champ contient un fichier uploadé sans erreur, false sinon.
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        $this->errorCode = is_array($value) ? ($value['error'] ?? UPLOAD_ERR_NO_FILE) : UPLOAD_ERR_NO_FILE;

        return $this->errorCode === UPLOAD_ERR_OK;
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
        if ($this->errorCode === UPLOAD_ERR_INI_SIZE || $this->errorCode === UPLOAD_ERR_FORM_SIZE) {
            return 'La photo est trop volumineuse (maximum autorisé dépassé).';
        }
        return 'Une erreur est survenue lors de l\'upload (code ' . $this->errorCode . ').';
    }
}