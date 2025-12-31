<?php

declare(strict_types=1);

namespace App\Twig;

use App\Services\LocaleRouteService;
use App\Services\TranslationService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * Twig extension providing translation and locale-aware routing functions.
 */
class TranslationExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * Creates a new TranslationExtension instance.
     *
     * @param TranslationService $translationService Translation service
     * @param LocaleRouteService $localeRouteService Locale route service
     */
    public function __construct(
        private readonly TranslationService $translationService,
        private readonly LocaleRouteService $localeRouteService,
    ) {
    }

    /**
     * Returns global variables for all Twig templates.
     *
     * @return array<string, mixed> Global template variables
     */
    public function getGlobals(): array
    {
        return [
            'locale' => $this->translationService->getLocale(),
            'default_locale' => $this->translationService->getDefaultLocale(),
            'supported_locales' => $this->translationService->getSupportedLocales(),
        ];
    }

    /**
     * Returns the list of Twig functions provided by this extension.
     *
     * @return list<TwigFunction> Twig functions
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('trans', $this->trans(...)),
            new TwigFunction('route', $this->routeLocalized(...)),
            new TwigFunction('language_switcher_urls', $this->languageSwitcherUrls(...)),
        ];
    }

    /**
     * Translates a message string.
     *
     * @param string                $id         Message identifier
     * @param array<string, string> $parameters Placeholder replacements
     * @param string|null           $domain     Translation domain
     *
     * @return string Translated message
     */
    public function trans(string $id, array $parameters = [], ?string $domain = null): string
    {
        return $this->translationService->trans($id, $parameters, $domain);
    }

    /**
     * Generates URL for a route in a specific locale.
     *
     * @param string      $routeName Route name
     * @param string|null $locale    Target locale (defaults to current)
     *
     * @return string Localized route path
     */
    public function routeLocalized(string $routeName, ?string $locale = null): string
    {
        $locale ??= $this->translationService->getLocale();
        return $this->localeRouteService->getLocalizedPath($routeName, $locale);
    }

    /**
     * Gets all localized URLs for current route for language switcher.
     *
     * @param string $currentRouteName Current route name
     *
     * @return array<string, string> Locale to URL mapping
     */
    public function languageSwitcherUrls(string $currentRouteName): array
    {
        return $this->localeRouteService->getAllLocalizedPaths($currentRouteName);
    }
}
