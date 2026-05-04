<?php

declare(strict_types=1);

/**
 * Retourne une valeur de configuration depuis les fichiers config/*.php.
 *
 * Fonctionne comme Laravel : config('app.name') charge config/app.php
 * et retourne la valeur à la clé 'name'.
 *
 * @param string $key     Clé pointée (ex: 'app.name', 'db.host')
 * @param mixed  $default Valeur par défaut si la clé est absente
 */
function config(string $key, mixed $default = null): mixed
{
    static $cache = [];

    [$file, $entry] = array_pad(explode('.', $key, 2), 2, '');

    if (! isset($cache[$file])) {
        $path = dirname(__DIR__) . '/config/' . $file . '.php';

        if (! file_exists($path)) {
            return $default;
        }

        $cache[$file] = require $path;
    }

    if ($entry === '') {
        return $cache[$file] ?? $default;
    }

    return $cache[$file][$entry] ?? $default;
}
