<?php

declare(strict_types=1);

/**
 * Point d'entrée HTTP de l'application.
 *
 * Charge l'application et exécute la requête.
 */

$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

$app->run();
