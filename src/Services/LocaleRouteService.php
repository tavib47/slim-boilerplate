<?php

declare(strict_types=1);

namespace App\Services;

class LocaleRouteService
{
    /** @var array<string, array<string, string>> */
    private array $routeSlugs;
    private string $defaultLocale;
    /** @var list<string> */
    private array $supportedLocales;

    /** @var array<string, array<string, string>> Reverse mapping: [locale][slug] => route_name */
    private array $slugToRoute = [];

    /**
     * @param array{
     *     default_locale: string,
     *     supported_locales: list<string>,
     *     route_slugs: array<string, array<string, string>>
     * } $config
     */
    public function __construct(array $config)
    {
        $this->routeSlugs = $config['route_slugs'] ?? [];
        $this->defaultLocale = $config['default_locale'];
        $this->supportedLocales = $config['supported_locales'];

        $this->buildSlugToRouteMap();
    }

    private function buildSlugToRouteMap(): void
    {
        foreach ($this->routeSlugs as $routeName => $translations) {
            foreach ($translations as $locale => $slug) {
                $this->slugToRoute[$locale][$slug] = $routeName;
            }
        }
    }

    /**
     * Get the translated slug for a route in a specific locale.
     */
    public function getSlugForRoute(string $routeName, string $locale): string
    {
        if ($locale === $this->defaultLocale) {
            return $routeName;
        }

        return $this->routeSlugs[$routeName][$locale] ?? $routeName;
    }

    /**
     * Get route name from a translated slug.
     */
    public function getRouteForSlug(string $slug, string $locale): ?string
    {
        if ($locale === $this->defaultLocale) {
            return $slug;
        }

        return $this->slugToRoute[$locale][$slug] ?? null;
    }

    /**
     * Generate a localized path for a route.
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
     * Get all localized versions of a route (for language switcher).
     *
     * @return array<string, string>
     */
    public function getAllLocalizedPaths(string $routeName): array
    {
        $paths = [];
        foreach ($this->supportedLocales as $locale) {
            $paths[$locale] = $this->getLocalizedPath($routeName, $locale);
        }
        return $paths;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * @return list<string>
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }
}
