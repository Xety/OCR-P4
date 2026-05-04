<?php

declare(strict_types=1);

/**
 * Seeder — génère des utilisateurs avec des mots de passe hachés.
 *
 * Usage : php database/seed.php
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$dsn = sprintf(
    'pgsql:host=%s;port=%s;dbname=%s',
    $_ENV['DB_HOST'],
    $_ENV['DB_PORT'],
    $_ENV['DB_NAME'],
);

$pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Utilisateurs demo (mot de passe : "password")
$users = [
    ['Alice Dupont', 'alice@example.com', 'password'],
    ['Bob Martin',   'bob@example.com',   'password'],
    ['Clara Petit',  'clara@example.com', 'password'],
];

$stmt = $pdo->prepare('
    INSERT INTO users (name, email, password)
    VALUES (:name, :email, :password)
');

foreach ($users as [$name, $email, $password]) {
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
    ]);
}

echo "\nSeeding terminé";
