<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Enregistre les routes et dispatche la requête vers le bon handler.
 */
final class Router
{
    /**
     * Table de routage : méthode HTTP => URI => handler
     *
     * @var array<string, array<string, callable>>
     */
    private array $routes = [];

    /**
     * Handler appelé quand aucune route ne correspond.
     *
     * @var callable|null
     */
    private mixed $fallback = null;

    /**
     * Enregistre un handler de fallback (404).
     */
    public function fallback(callable $handler): void
    {
        $this->fallback = $handler;
    }

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
     */
    public function dispatch(Request $request): void
    {
        $handler = $this->routes[$request->method->value][$request->uri] ?? $this->fallback;

        if ($handler !== null) {
            echo ($handler)($request);
        }
    }
}
