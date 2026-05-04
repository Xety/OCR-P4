# OCR-P4 — Développement du site de TomTroc

## Stack technique

| Outil | Rôle |
|---|---|
| PHP 8.4+ | Langage principal |
| Composer | Autoloading PSR-4 + gestion de dépendances |
| vlucas/phpdotenv | Lecture du fichier `.env` |
| PostgreSQL | Base de données (via PDO) |

---

## Vie d'une requête HTTP

Voici le chemin parcouru par une requête HTTP, de l'entrée jusqu'à l'affichage de la réponse.

```
Navigateur
    │
    │  GET /users
    ▼
public/index.php          ← Point d'entrée unique (front controller)
    │
    │  charge bootstrap/app.php
    ▼
bootstrap/app.php         ← Charge .env, valide les variables requises
    │                        Instancie Application et la retourne
    ▼
Application::__construct()
    │  ├─ Database::getInstance()   → connexion PDO PostgreSQL (singleton)
    │  ├─ new View($basePath)       → moteur de rendu
    │  ├─ new Router()              → table de routage vide
    │  └─ registerRoutes()          → enregistre les routes (GET /, GET /users…)
    │
Application::run()
    │  └─ new Request()             → capture méthode HTTP, URI, query string, body
    │
Router::dispatch(Request)
    │  └─ cherche $routes[GET][/users]
    │     ├─ trouvé  → appelle le handler (callable)
    │     └─ introuvable → http_response_code(404) + message d'erreur
    │
UserController::index(Request)    ← handler trouvé
    │  ├─ UserRepository::all()    → exécute SELECT en base
    │  │    retourne array<UserEntity> (objets readonly immuables)
    │  └─ $this->view->render('users/index', ['users' => $users, ...])
    │
View::render(string $view, array $data)
    │  ├─ localise le fichier  resources/views/users/index.php
    │  ├─ renderTemplate()     → ob_start / extract($data) / require / ob_get_clean()
    │  │    capture le HTML du contenu de la page
    │  └─ renderTemplate()     → injecte $content dans resources/views/layout.php
    │       retourne le HTML complet
    │
Router::dispatch()
    │  └─ echo $html           → envoie la réponse au navigateur
    ▼
Navigateur affiche la page
```

---

## Structure des dossiers

```
app/
├── Contracts/     Interfaces (RendererInterface, ModelInterface)
├── Controllers/   Contrôleurs (un par ressource)
├── Core/          Noyau du framework (Application, Router, View, Request, Database…)
├── Entities/      Objets de données immuables (readonly, sans PDO)
├── Enums/         Énumérations PHP (HttpMethod)
├── helpers.php    Fonctions globales (config())
└── Repositories/  Accès base de données — retournent des Entities
bootstrap/
└── app.php        Point de démarrage : charge .env et instancie Application
config/
├── app.php        Configuration application (name, env…)
└── db.php         Configuration base de données (host, port, name…)
database/
└── schema.sql     Schéma SQL à exécuter manuellement
public/
├── index.php      Front controller (seul fichier web-accessible)
├── css/style.css
└── js/header.js
resources/
└── views/         Templates PHP
    ├── layout.php
    ├── partials/  (header, footer)
    └── users/
```

---

## Installation

```bash
# 1. Installer les dépendances
composer install

# 2. Copier et remplir le fichier d'environnement
cp .env.example .env

# 3. Créer la base de données PostgreSQL puis exécuter le schéma
psql -U postgres -d ocr_p4 -f database/schema.sql
```

Configurer un virtual host ou utiliser le serveur intégré PHP :

```bash
php -S localhost:8000 -t public
```
