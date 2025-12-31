<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Services\LocaleTemplateResolver;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

/**
 * Controller for the homepage.
 */
class HomeController
{
    /**
     * Creates a new HomeController instance.
     *
     * @param Twig $twig
     *   Twig view renderer.
     * @param LocaleTemplateResolver $templateResolver
     *   Template resolver for locale-specific templates.
     */
    public function __construct(
        private readonly Twig $twig,
        private readonly LocaleTemplateResolver $templateResolver,
    ) {
    }

    /**
     * Renders the homepage.
     *
     * @param Request $request
     *   HTTP request.
     * @param Response $response
     *   HTTP response.
     *
     * @return Response Rendered homepage response
     */
    public function index(Request $request, Response $response): Response
    {
        $locale = $request->getAttribute('locale', 'en');
        $defaultLocale = $request->getAttribute('default_locale', 'en');

        $templatePath = $this->templateResolver->resolve(
            'pages/home.twig',
            $locale,
            $defaultLocale,
        );

        return $this->twig->render($response, $templatePath, [
            'flash' => SessionMiddleware::getFlash(),
            'current_route' => 'home',
        ]);
    }
}
