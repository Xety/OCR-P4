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
    RETURNING id
');

$aliceId = null;

foreach ($users as $i => [$name, $email, $password]) {
    $stmt->execute([
        'name'     => $name,
        'email'    => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
    ]);

    $row = $stmt->fetch();

    // On retient l'ID du premier utilisateur (Alice) pour les livres de démo
    if ($i === 0) {
        $aliceId = (int) $row['id'];
    }
}

echo "\nUtilisateurs seedés";

// ---- Livres de démo pour Alice ----
$books = [
    [$aliceId, 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'Un aviateur tombe en panne dans le désert et rencontre un mystérieux petit garçon venu d\'une autre planète.', true],
    [$aliceId, 'L\'Alchimiste', 'Paulo Coelho', 'Le voyage initiatique d\'un jeune berger andalou à la recherche d\'un trésor enfoui au pied des Pyramides.', false],
    [$aliceId, 'The Kinkfolk Table', 'Nathan Williams', 'J\'ai récemment plongé dans les pages de \'The Kinkfolk Table\' et j\'ai été enchanté par chaque recette.', true],
    [$aliceId, 'Harry Potter à l\'école', 'J.K. Rowling', 'Un jeune garçon découvre le jour de ses onze ans qu\'il est un sorcier et intègre l\'école de Poudlard.', false],
];

$stmtBook = $pdo->prepare('
    INSERT INTO books (user_id, title, author, description, is_available)
    VALUES (:user_id, :title, :author, :description, :is_available)
');

foreach ($books as [$userId, $title, $author, $description, $isAvailable]) {
    $stmtBook->execute([
        'user_id' => $userId,
        'title' => $title,
        'author' => $author,
        'description' => $description,
        'is_available' => $isAvailable ? 'true' : 'false',
    ]);
}

echo "\nLivres seedés\n";
