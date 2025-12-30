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

    public function about(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/about.twig', [
            'flash' => SessionMiddleware::getFlash(),
        ]);
    }

    public function privacy(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/privacy.twig', [
            'flash' => SessionMiddleware::getFlash(),
        ]);
    }
}
