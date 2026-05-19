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
├── Contracts/                  Interfaces (RendererInterface, RuleInterface)
├── Controllers/                Contrôleurs (un par ressource)
├── Core/                       Noyau du framework
│   ├── ORM/
│   │   ├── AbstractEntity.php      Base entities : fill(), toRow(), fromRow()
│   │   └── AbstractRepository.php  Base repositories : find(), save(), delete()
│   ├── Helpers/
│   │   ├── DateHelper.php
│   │   └── StringHelper.php
│   ├── Application.php         Bootstrap : dépendances + enregistrement des routes
│   ├── Auth.php                Session PHP (login, logout, user())
│   ├── Database.php            Singleton PDO PostgreSQL
│   ├── HttpMethod.php          Enum (Get, Post, Put, Patch, Delete)
│   ├── Redirect.php
│   ├── Request.php             Encapsule $_SERVER, $_GET, $_POST + method spoofing
│   ├── Router.php              Table de routage + dispatch (statique + dynamique)
│   └── View.php                Moteur de rendu PHP (layout + extract)
├── Entities/                   Objets de données (sans PDO)
├── Repositories/               Accès base de données — retournent des Entities
├── Validation/                 Système de validation des formulaires
│   ├── Rules/                  (Required, Email, MinLength, Confirmed…)
│   └── Validator.php
└── helpers.php                 Fonctions globales (config(), e())
bootstrap/
└── app.php        Point de démarrage : charge .env et instancie Application
config/
├── app.php        Configuration application (name, env…)
├── db.php         Configuration base de données (host, port, name…)
└── view.php       Configuration des vues (chemin, layout)
database/
├── schema.sql     Schéma SQL (PostgreSQL)
└── seed.php       Données de test (utilisateurs, livres)
public/
├── index.php      Front controller (seul fichier web-accessible)
├── css/style.css
├── js/app.js
└── images/
    └── books/     Photos de couvertures uploadées
resources/
└── views/         Templates PHP
```

---

## Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/Xety/OCR-P4.git
cd OCR-P4

# 2. Installer les dépendances
composer install

# 3. Copier et remplir le fichier d'environnement
cp .env.example .env

# 4. Créer la base de données PostgreSQL puis exécuter le schéma
psql -U postgres -d ocr_p4 -f database/schema.sql

#5. Optionnel : Seeder la base de donnée avec des données génériques :
php database/seed.php

# Comptes génériques :
# alice@example.com | password
# bob@example.com | password
# clara@example.com | password
```
