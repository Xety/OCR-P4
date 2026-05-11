<?php

declare(strict_types=1);

namespace App\Core\Helpers;

final class StringHelper
{
    /**
     * Échappe une chaîne pour un affichage sécurisé en HTML (XSS).
     *
     * @param string $value La valeur à échapper.
     *
     * @return string La valeur échappée.
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
