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
    │  ├─ new User($db)            → instancie le modèle avec la connexion PDO
    │  ├─ $user->all()             → exécute SELECT en base, retourne array<User>
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

### En résumé

1. **`public/index.php`** — seul fichier accessible depuis le web. Il délègue immédiatement.
2. **`bootstrap/app.php`** — prépare l'environnement et construit l'`Application`.
3. **`Application`** — orchestre les dépendances (PDO, View, Router) et enregistre les routes.
4. **`Router`** — fait correspondre `[méthode][URI]` à un callable et l'exécute.
5. **`Request`** — encapsule les données de la requête HTTP (immuable, readonly).
6. **`Controller`** — reçoit la `Request`, interroge le modèle, demande à la vue de rendre le résultat.
7. **`Model`** — exécute les requêtes SQL via PDO et retourne des objets typés.
8. **`View`** — assemble le contenu de la vue dans le layout et retourne le HTML final.

---

## Structure des dossiers

```
app/
├── Controllers/   Contrôleurs (un par ressource)
├── Core/          Noyau du framework (Application, Router, View, Request, Database…)
├── Enums/         Énumérations PHP (HttpMethod)
├── Models/        Modèles Eloquent-like (accès base de données)
bootstrap/
└── app.php        Point de démarrage : charge .env et instancie Application
config/
└── app.php        Helper config() — lit les variables d'environnement
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
