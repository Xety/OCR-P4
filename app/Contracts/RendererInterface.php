<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Contrat pour le rendu de vues.
 *
 * Respecte le principe D de SOLID : dépendances inversion.
 *
 * Il définit qu'un "renderer" doit exposer une méthode render(string $view, array $data): string,
 * sans imposer comment elle est implémentée. Cela permet de changer facilement de moteur de templates
 * sans modifier les contrôleurs qui l'utilisent
 */
interface RendererInterface
{
    /**
     * Rend une vue et retourne son contenu HTML.
     *
     * @param  string $view Nom de la vue (ex: 'users/index')
     * @param  array $data Variables transmises à la vue
     *
     * @return string Contenu HTML rendu de la vue
     */
    public function render(string $view, array $data = []): string;
}
