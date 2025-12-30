<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Services\LocaleTemplateResolver;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class PageController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly LocaleTemplateResolver $templateResolver
    ) {
    }

    public function show(Request $request, Response $response): Response
    {
        $locale = $request->getAttribute('locale', 'en');
        $defaultLocale = $request->getAttribute('default_locale', 'en');

        // Get route name for language switcher
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $routeName = $route?->getName() ?? '';

        // Strip locale suffix from route name to get base name (e.g., about.ro -> about)
        $baseRouteName = preg_replace('/\.[a-z]{2}$/', '', $routeName);

        // Template name is the base route name
        $template = $baseRouteName ?: trim($request->getUri()->getPath(), '/') ?: 'home';

        // Resolve template with locale override check
        $templatePath = $this->templateResolver->resolve(
            "pages/{$template}.twig",
            $locale,
            $defaultLocale
        );

        return $this->twig->render($response, $templatePath, [
            'flash' => SessionMiddleware::getFlash(),
            'current_route' => $baseRouteName,
        ]);
    }
}
