<?php

declare(strict_types=1);

/**
 * Retourne une valeur de configuration chargée depuis les variables d'environnement.
 *
 * @param string $key La clé (ex: 'app.name', 'db.host')
 * @param mixed $default Valeur par défaut si la clé n'existe pas
 *
 * @return mixed
 */
function config(string $key, mixed $default = null): mixed
{
    /** @var array<string, array<string, string>> $map */
    static $map = [
        'app' => [
            'name' => 'APP_NAME',
            'env' => 'APP_ENV',
        ],
        'db' => [
            'host' => 'DB_HOST',
            'port' => 'DB_PORT',
            'name' => 'DB_NAME',
            'user' => 'DB_USER',
            'password' => 'DB_PASSWORD',
        ],
    ];

    [$section, $entry] = array_pad(explode('.', $key, 2), 2, '');

    $envKey = $map[$section][$entry] ?? null;

    if ($envKey === null) {
        return $default;
    }

    $value = $_ENV[$envKey] ?? $default;

    return $value;
}
