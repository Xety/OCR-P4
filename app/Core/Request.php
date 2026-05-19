<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Représente la requête HTTP courante.
 *
 * Encapsule les superglobales $_SERVER, $_GET et $_POST
 * pour éviter d'y accéder directement dans le reste du code.
 */
final class Request
{
    /**
     *  L'URI de la requête, sans les paramètres de requête.
     *
     * @var string
     */
    public readonly string $uri;
    /**
     *  La méthode HTTP de la requête.
     *
     * @var HttpMethod
     */
    public readonly HttpMethod $method;

    /**
     *  Les paramètres de requête (query string).
     *
     * @var array<string, string>
     */
    public readonly array $query;

    /**
     *  Les paramètres de la requête (body).
     *
     * @var array<string, string>
     */
    public readonly array $body;

    /**
     * Les paramètres extraits des segments dynamiques de l'URI (ex: {id}).
     *
     * @var array<string, string>
     */
    public array $params = [];

    public function __construct()
    {
        $rawUri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->uri = parse_url($rawUri, PHP_URL_PATH) ?: '/';
        $this->query = $_GET;
        $this->body = $_POST;

        $rawMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Method spoofing : <input type="hidden" name="_method" value="DELETE">
        if ($rawMethod === 'POST' && isset($_POST['_method'])) {
            $rawMethod = strtoupper($_POST['_method']);
        }

        $this->method = HttpMethod::from($rawMethod);
    }
}
