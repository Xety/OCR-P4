<?php

declare(strict_types=1);

namespace App\Core\Helpers;

use DateTimeImmutable;

final class DateHelper
{
    /**
     * Retourne une chaîne lisible décrivant l'ancienneté d'une date.
     * Exemples :
     * - "2 ans" pour une date il y a 2 ans
     * - "moins d'un mois" pour une date il y a moins d'un mois
     *
     * @param DateTimeImmutable $date La date de référence.
     *
     * @return string Une chaîne décrivant le temps écoulé depuis la date.
     */
    public static function elapsed(DateTimeImmutable $date): string
    {
        $diff = (new DateTimeImmutable())->diff($date);

        if ($diff->y >= 1) {
            return $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        }

        if ($diff->m >= 1) {
            return $diff->m . ' mois';
        }

        return 'moins d\'un mois';
    }
}
