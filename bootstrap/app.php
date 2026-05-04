<?php

declare(strict_types=1);

use App\Core\Application;
use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Charge les variables d'environnement depuis .env
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$dotenv->required(['APP_NAME', 'APP_ENV', 'DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']);

// Crée et retourne l'instance centrale de l'application, qui sera utilisée dans public/index.php
return new Application(dirname(__DIR__));
