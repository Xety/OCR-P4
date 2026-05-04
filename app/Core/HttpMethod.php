<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Méthodes HTTP supportées par le routeur.
 */
enum HttpMethod: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Put = 'PUT';
    case Patch = 'PATCH';
    case Delete = 'DELETE';
}
