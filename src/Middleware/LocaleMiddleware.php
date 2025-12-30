<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\LocaleRouteService;
use App\Services\TranslationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LocaleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly TranslationService $translationService,
        private readonly LocaleRouteService $localeRouteService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        $segments = array_values(array_filter(explode('/', $path), static fn ($s) => $s !== ''));

        $supportedLocales = $this->localeRouteService->getSupportedLocales();
        $defaultLocale = $this->localeRouteService->getDefaultLocale();
        $locale = $defaultLocale;

        // Check if first segment is a locale prefix
        if (!empty($segments[0]) && in_array($segments[0], $supportedLocales, true)) {
            $locale = $segments[0];

            // Only strip prefix for non-default locales
            if ($locale !== $defaultLocale) {
                array_shift($segments);

                // Resolve translated slug to original route slug
                if (!empty($segments[0])) {
                    $slug = $segments[0];
                    $routeName = $this->localeRouteService->getRouteForSlug($slug, $locale);

                    if ($routeName !== null && $routeName !== $slug) {
                        $segments[0] = $routeName;
                    }
                }

                $strippedPath = '/' . implode('/', $segments);

                // Update request URI with stripped path
                $uri = $request->getUri()->withPath($strippedPath);
                $request = $request->withUri($uri);
            }
        }

        // Set locale on translation service
        $this->translationService->setLocale($locale);

        // Add locale info to request attributes
        $request = $request
            ->withAttribute('locale', $locale)
            ->withAttribute('default_locale', $defaultLocale);

        return $handler->handle($request);
    }
}
