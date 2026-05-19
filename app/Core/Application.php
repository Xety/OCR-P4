<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\AccountController;
use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\ConversationController;
use App\Controllers\ErrorController;
use App\Controllers\PageController;
use App\Controllers\UserController;
use PDO;

/**
 * Point central du framework : charge l'environnement,
 * instancie les dépendances et dispatche la requête.
 */
final class Application
{
    /**
     * Le routeur de l'application.
     * @var Router
     */
    private Router $router;

    /**
     * Le moteur de rendu des vues.
     * @var View
     */
    private View $view;

    /**
     * L'instance de connexion à la base de données.
     * @var PDO
     */
    private PDO $db;

    /**
     * Initialise l'application : charge la configuration, crée les dépendances et enregistre les routes.
     *
     * @param string $basePath Le chemin de base de l'application.
     */
    public function __construct(private readonly string $basePath)
    {
        $this->db = Database::getInstance();
        $this->view = new View($this->basePath);
        $this->router = new Router();

        $this->registerRoutes();
    }

    /**
     * Démarre l'application : crée la requête et dispatche vers le bon contrôleur.
     */
    public function run(): void
    {
        $request = new Request();
        $this->router->dispatch($request);
    }

    /**
     * Déclare toutes les routes de l'application.
     *
     * L'ajout d'une route n'implique aucune modification du Router.
     */
    private function registerRoutes(): void
    {
        $pageController = new PageController($this->view, $this->db);
        $this->router->get('/', $pageController->index(...));

        $bookController = new BookController($this->view, $this->db);
        $this->router->get('/books', $bookController->index(...));
        $this->router->get('/books/{id}/edit', $bookController->edit(...));
        $this->router->post('/books/{id}/edit', $bookController->update(...));
        $this->router->get('/books/{id}', $bookController->show(...));

        $authController = new AuthController($this->view, $this->db);
        $this->router->get('/login', $authController->showLogin(...));
        $this->router->post('/login', $authController->login(...));
        $this->router->get('/register', $authController->showRegister(...));
        $this->router->post('/register', $authController->register(...));
        $this->router->get('/logout', $authController->logout(...));

        $accountController = new AccountController($this->view, $this->db);
        $this->router->get('/account', $accountController->index(...));
        $this->router->post('/account', $accountController->update(...));

        $userController = new UserController($this->view, $this->db);
        $this->router->get('/users/{id}', $userController->show(...));

        $conversationController = new ConversationController($this->view, $this->db);
        $this->router->get('/conversations', $conversationController->index(...));
        $this->router->post('/conversations', $conversationController->create(...));
        $this->router->get('/conversations/{id}', $conversationController->show(...));
        $this->router->post('/conversations/{id}/messages', $conversationController->store(...));

        $errorController = new ErrorController($this->view, $this->db);
        $this->router->fallback($errorController->notFound(...));
    }
}
