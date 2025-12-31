<?php

declare(strict_types=1);

use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// Route definitions: 'name' => ['method' => ..., 'path' => ..., 'handler' => [...]]
$routes = [
    'home' => [
        'method' => 'GET',
        'path' => '/',
        'handler' => [HomeController::class, 'index'],
    ],
    'about' => [
        'method' => 'GET',
        'path' => '/about',
        'handler' => [PageController::class, 'show'],
    ],
    'privacy' => [
        'method' => 'GET',
        'path' => '/privacy',
        'handler' => [PageController::class, 'show'],
    ],
    'contact' => [
        'method' => 'GET',
        'path' => '/contact',
        'handler' => [PageController::class, 'show'],
    ],
    'contact.submit' => [
        'method' => 'POST',
        'path' => '/contact',
        'handler' => [ContactController::class, 'submit'],
    ],
];

return static function (App $app) use ($routes): void {
    $settings = $app->getContainer()->get('settings');
    $localeConfig = $settings['locale'];
    $defaultLocale = $localeConfig['default_locale'];
    $supportedLocales = $localeConfig['supported_locales'];
    $routeSlugs = $localeConfig['route_slugs'];

    $registerRoutes = static function (
        RouteCollectorProxy $group,
        array $routes,
        string $locale,
        string $defaultLocale,
        array $routeSlugs,
        bool $isGroup = true
    ): void {
        $nameSuffix = $locale !== $defaultLocale ? ".{$locale}" : '';

        foreach ($routes as $name => $route) {
            $method = $route['method'];
            $path = $route['path'];
            $handler = $route['handler'];

            // Translate path for non-default locales
            if ($path !== '/') {
                $slug = ltrim($path, '/');
                // For routes like 'contact.submit', use the base name for slug lookup
                $slugKey = explode('.', $name)[0];
                $hasTranslation = $locale !== $defaultLocale && isset($routeSlugs[$slugKey][$locale]);
                $translatedSlug = $hasTranslation ? $routeSlugs[$slugKey][$locale] : $slug;
                $path = '/' . $translatedSlug;
            } elseif ($isGroup) {
                // Home route: use '' in groups, '/' otherwise
                $path = '';
            }

            $group->map([$method], $path, $handler)->setName($name . $nameSuffix);
        }
    };

    // Default locale routes (no prefix)
    $registerRoutes($app, $routes, $defaultLocale, $defaultLocale, $routeSlugs, false);

    // Prefixed routes for other locales
    foreach ($supportedLocales as $locale) {
        if ($locale !== $defaultLocale) {
            // phpcs:ignore SlevomatCodingStandard.Functions.StaticClosure.ClosureNotStatic
            $app->group("/{$locale}", function (RouteCollectorProxy $group) use (
                $routes,
                $locale,
                $defaultLocale,
                $routeSlugs,
                $registerRoutes
            ): void {
                $registerRoutes($group, $routes, $locale, $defaultLocale, $routeSlugs);
            });
        }
    }
};
