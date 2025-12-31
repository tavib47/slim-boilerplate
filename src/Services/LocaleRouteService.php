<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Service for managing locale-aware route slugs and paths.
 */
class LocaleRouteService
{
    /** @var array<string, array<string, string>> Route name to locale slug mappings */
    private array $routeSlugs;

    /** @var string Default application locale */
    private string $defaultLocale;

    /** @var list<string> Supported locale codes */
    private array $supportedLocales;

    /** @var array<string, array<string, string>> Reverse mapping: [locale][slug] => route_name */
    private array $slugToRoute = [];

    /**
     * Creates a new LocaleRouteService instance.
     *
     * @param array<string, mixed> $config Locale configuration with keys:
     *                                      default_locale, supported_locales, route_slugs
     */
    public function __construct(array $config)
    {
        $this->routeSlugs = $config['route_slugs'] ?? [];
        $this->defaultLocale = $config['default_locale'];
        $this->supportedLocales = $config['supported_locales'];

        $this->buildSlugToRouteMap();
    }

    /**
     * Builds reverse mapping from slugs to route names per locale.
     *
     * @return void
     */
    private function buildSlugToRouteMap(): void
    {
        foreach ($this->routeSlugs as $routeName => $translations) {
            foreach ($translations as $locale => $slug) {
                $this->slugToRoute[$locale][$slug] = $routeName;
            }
        }
    }

    /**
     * Gets the translated slug for a route in a specific locale.
     *
     * @param string $routeName Route name
     * @param string $locale    Target locale
     *
     * @return string Translated slug or original route name
     */
    public function getSlugForRoute(string $routeName, string $locale): string
    {
        if ($locale === $this->defaultLocale) {
            return $routeName;
        }

        return $this->routeSlugs[$routeName][$locale] ?? $routeName;
    }

    /**
     * Gets route name from a translated slug.
     *
     * @param string $slug   URL slug
     * @param string $locale Current locale
     *
     * @return string|null Route name or null if not found
     */
    public function getRouteForSlug(string $slug, string $locale): ?string
    {
        if ($locale === $this->defaultLocale) {
            return $slug;
        }

        return $this->slugToRoute[$locale][$slug] ?? null;
    }

    /**
     * Generates a localized path for a route.
     *
     * @param string $routeName    Route name
     * @param string $targetLocale Target locale
     *
     * @return string Full localized path
     */
    public function getLocalizedPath(string $routeName, string $targetLocale): string
    {
        $slug = $this->getSlugForRoute($routeName, $targetLocale);

        if ($routeName === 'home') {
            if ($targetLocale === $this->defaultLocale) {
                return '/';
            }
            return '/' . $targetLocale;
        }

        if ($targetLocale === $this->defaultLocale) {
            return '/' . $slug;
        }

        return '/' . $targetLocale . '/' . $slug;
    }

    /**
     * Gets all localized versions of a route for language switcher.
     *
     * @param string $routeName Route name
     *
     * @return array<string, string> Locale to path mapping
     */
    public function getAllLocalizedPaths(string $routeName): array
    {
        $paths = [];
        foreach ($this->supportedLocales as $locale) {
            $paths[$locale] = $this->getLocalizedPath($routeName, $locale);
        }
        return $paths;
    }

    /**
     * Gets the default application locale.
     *
     * @return string Default locale code
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Gets the list of supported locales.
     *
     * @return list<string> Array of supported locale codes
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }
}
