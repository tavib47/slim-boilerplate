<?php

declare(strict_types=1);

use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {
    $settings = $app->getContainer()->get('settings');
    $localeConfig = $settings['locale'];
    $defaultLocale = $localeConfig['default_locale'];
    $supportedLocales = $localeConfig['supported_locales'];
    $routeSlugs = $localeConfig['route_slugs'];

    $defineRoutes = static function (
        RouteCollectorProxy $group,
        string $locale,
        array $routeSlugs,
        string $defaultLocale,
        bool $isGroup = true
    ): void {
        $nameSuffix = $locale !== $defaultLocale ? ".{$locale}" : '';

        // Home page - use '/' when not in a group, '' when in a group
        $homePath = $isGroup ? '' : '/';
        $group->get($homePath, [HomeController::class, 'index'])->setName('home' . $nameSuffix);

        // Static pages with translated slugs
        $staticPages = ['about', 'privacy', 'contact'];
        foreach ($staticPages as $routeName) {
            $hasTranslation = $locale !== $defaultLocale && isset($routeSlugs[$routeName][$locale]);
            $slug = $hasTranslation ? $routeSlugs[$routeName][$locale] : $routeName;

            $group->get("/{$slug}", [PageController::class, 'show'])->setName($routeName . $nameSuffix);
        }

        // Contact form submission
        $hasContactTranslation = $locale !== $defaultLocale && isset($routeSlugs['contact'][$locale]);
        $contactSlug = $hasContactTranslation ? $routeSlugs['contact'][$locale] : 'contact';
        $group->post("/{$contactSlug}", [ContactController::class, 'submit'])
            ->setName('contact.submit' . $nameSuffix);
    };

    // Default locale routes (no prefix)
    $defineRoutes($app, $defaultLocale, $routeSlugs, $defaultLocale, false);

    // Prefixed routes for other locales
    foreach ($supportedLocales as $locale) {
        if ($locale !== $defaultLocale) {
            // phpcs:ignore SlevomatCodingStandard.Functions.StaticClosure.ClosureNotStatic
            $callback = function (RouteCollectorProxy $group) use (
                $locale,
                $routeSlugs,
                $defaultLocale,
                $defineRoutes
            ): void {
                $defineRoutes($group, $locale, $routeSlugs, $defaultLocale);
            };
            $app->group("/{$locale}", $callback);
        }
    }
};
