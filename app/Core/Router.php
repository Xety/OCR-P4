<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Enregistre les routes et dispatche la requête vers le bon handler.
 */
final class Router
{
    /**
     * Table de routage : méthode HTTP => URI => handler
     *
     * @var array
     */
    private array $routes = [];

    /**
     * Enregistre une route pour la méthode HTTP donnée.
     */
    public function route(HttpMethod $method, string $uri, callable $handler): void
    {
        $this->routes[$method->value][$uri] = $handler;
    }

    /**
     * Enregistre une route GET.
     *
     * @param string $uri L'URI de la route.
     * @param callable $handler Le handler à exécuter.
     *
     * @return void
     */
    public function get(string $uri, callable $handler): void
    {
        $this->route(HttpMethod::Get, $uri, $handler);
    }

    /**
     * Enregistre une route POST.
     *
     * @param string $uri L'URI de la route.
     * @param callable $handler Le handler à exécuter.
     *
     * @return void
     */
    public function post(string $uri, callable $handler): void
    {
        $this->route(HttpMethod::Post, $uri, $handler);
    }

    /**
     * Enregistre une route PUT.
     *
     * @param string $uri L'URI de la route.
     * @param callable $handler Le handler à exécuter.
     *
     * @return void
     */
    public function put(string $uri, callable $handler): void
    {
        $this->route(HttpMethod::Put, $uri, $handler);
    }

    /**
     * Enregistre une route PATCH.
     *
     * @param string $uri L'URI de la route.
     * @param callable $handler Le handler à exécuter.
     *
     * @return void
     */
    public function patch(string $uri, callable $handler): void
    {
        $this->route(HttpMethod::Patch, $uri, $handler);
    }

    /**
     * Enregistre une route DELETE.
     *
     * @param string $uri L'URI de la route.
     * @param callable $handler Le handler à exécuter.
     *
     * @return void
     */
    public function delete(string $uri, callable $handler): void
    {
        $this->route(HttpMethod::Delete, $uri, $handler);
    }

    /**
     * Dispatche la requête vers le handler correspondant.
     *
     * @throws RuntimeException si aucune route ne correspond
     */
    public function dispatch(Request $request): void
    {
        $handler = $this->routes[$request->method->value][$request->uri] ?? null;

        // Si aucune route ne correspond, envoyer une 404.
        if ($handler === null) {
            $this->sendNotFound($request->uri);
            return;
        }

        // Exécuter le handler et envoyer la réponse.
        echo ($handler)($request);
    }

    /**
     * Envoie une réponse 404 simple.
     */
    private function sendNotFound(string $uri): void
    {
        http_response_code(404);
        echo sprintf('<h1>404 — Page introuvable</h1><p>La route <code>%s</code> n\'existe pas.</p>', htmlspecialchars($uri));
    }
}
