<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class PageController
{
    public function __construct(
        private readonly Twig $twig
    ) {
    }

    public function show(Request $request, Response $response): Response
    {
        // Get template name from URL path (e.g., /about -> about)
        $path = trim($request->getUri()->getPath(), '/');
        $template = $path ?: 'home';

        return $this->twig->render($response, "pages/{$template}.twig", [
            'flash' => SessionMiddleware::getFlash(),
        ]);
    }
}
