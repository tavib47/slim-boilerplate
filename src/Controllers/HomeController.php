<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Services\LocaleTemplateResolver;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly LocaleTemplateResolver $templateResolver
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        $locale = $request->getAttribute('locale', 'en');
        $defaultLocale = $request->getAttribute('default_locale', 'en');

        $templatePath = $this->templateResolver->resolve(
            'pages/home.twig',
            $locale,
            $defaultLocale
        );

        return $this->twig->render($response, $templatePath, [
            'flash' => SessionMiddleware::getFlash(),
            'current_route' => 'home',
        ]);
    }
}
