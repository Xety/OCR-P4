<?php

declare(strict_types=1);

namespace App\Validation;

/**
 * Orchestre les règles de validation sur des données.
 *
 * Usage :
 *   $validator = new Validator($request->body, [
 *       'email'    => [new Required(), new Email()],
 *       'password' => [new Required(), new MinLength(8)],
 *   ]);
 *
 *   if ($validator->fails()) {
 *       $error = $validator->firstError();
 *   }
 */
final class Validator
{
    /**
     * Erreurs indexées par champ : ['email' => ['message1', ...], ...]
     *
     * @var array
     */
    private array $errors = [];

    /**
     * @param array $data Données à valider (ex: $request->body)
     * @param array $rules Règles par champ
     */
    public function __construct(
        private readonly array $data,
        private readonly array $rules,
    ) {
        $this->validate();
    }

    /**
     * Retourne true si au moins une règle a échoué.
     *
     * @return bool True si au moins une règle a échoué, false sinon.
     */
    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /**
     * Retourne toutes les erreurs indexées par champ.
     *
     * @return array Erreurs indexées par champ : ['email' => ['message1', ...], ...]
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne le premier message d'erreur rencontré, ou null si tout est valide.
     *
     * @return string|null Le premier message d'erreur, ou null si tout est valide.
     */
    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }

        return null;
    }

    /**
     * Exécute toutes les règles et peuple $this->errors.
     *
     * @return void
     */
    private function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                if (! $rule->passes($field, $value, $this->data)) {
                    $this->errors[$field][] = $rule->message($field);
                }
            }
        }
    }
}
