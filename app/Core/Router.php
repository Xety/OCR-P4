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
        $methodRoutes = $this->routes[$request->method->value] ?? [];

        // Correspondance exacte (route statique)
        if (isset($methodRoutes[$request->uri])) {
            echo ($methodRoutes[$request->uri])($request);
            return;
        }

        // Correspondance par pattern (routes dynamiques avec {param})
        foreach ($methodRoutes as $pattern => $handler) {
            [$matched, $params] = $this->matchPattern($pattern, $request->uri);
            if ($matched) {
                // Injecte les paramètres extraits de l'URI dans la requête avant d'appeler le handler
                $request->params = $params;
                echo ($handler)($request);
                return;
            }
        }

        // Fallback 404
        if ($this->fallback !== null) {
            echo ($this->fallback)($request);
        }
    }

    /**
     * Teste si un pattern de route correspond à l'URI de la requête.
     *
     * @param string $pattern Le pattern de la route.
     * @param string $uri L'URI de la requête.
     *
     * @return array [bool $matched, array $params] - $matched indique si le pattern correspond, $params contient les paramètres extraits de l'URI
     */
    private function matchPattern(string $pattern, string $uri): array
    {
        // Convertit un pattern comme "/books/{id}" en regex avec groupe nommé : "#^/books/(?P<id>[^/]+)$#"
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);

        if (! preg_match('#^' . $regex . '$#', $uri, $matches)) {
            return [false, []];
        }

        // Filtre les clés numériques de $matches pour ne garder que les paramètres nommés
        // (ex: ['id' => '42', 0 => '/books/42', 1 => '42'] => ['id' => '42'])
        $params = array_filter(
            $matches,
            fn (int|string $key): bool => is_string($key),
            ARRAY_FILTER_USE_KEY,
        );

        return [true, $params];
    }
}
