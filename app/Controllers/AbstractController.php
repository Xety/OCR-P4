<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\RendererInterface;
use PDO;

/**
 * Classe de base pour tous les contrôleurs.
 *
 * Centralise l'injection des dépendances communes afin d'éviter de les
 * répéter dans chaque contrôleur (principe DRY + principe D de SOLID).
 */
abstract class AbstractController
{
    public function __construct(
        protected readonly RendererInterface $view,
        protected readonly PDO $db,
    ) {}
}
