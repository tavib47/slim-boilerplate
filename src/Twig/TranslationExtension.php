<?php

declare(strict_types=1);

namespace App\Twig;

use App\Services\LocaleRouteService;
use App\Services\TranslationService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class TranslationExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly TranslationService $translationService,
        private readonly LocaleRouteService $localeRouteService
    ) {
    }

    /**
     * @return array<string, mixed>
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
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('trans', $this->trans(...)),
            new TwigFunction('route_localized', $this->routeLocalized(...)),
            new TwigFunction('language_switcher_urls', $this->languageSwitcherUrls(...)),
        ];
    }

    /**
     * @param array<string, string> $parameters
     */
    public function trans(string $id, array $parameters = [], ?string $domain = null): string
    {
        return $this->translationService->trans($id, $parameters, $domain);
    }

    /**
     * Generate URL for a route in a specific locale.
     */
    public function routeLocalized(string $routeName, string $locale): string
    {
        return $this->localeRouteService->getLocalizedPath($routeName, $locale);
    }

    /**
     * Get all localized URLs for current route (for language switcher).
     *
     * @return array<string, string>
     */
    public function languageSwitcherUrls(string $currentRouteName): array
    {
        return $this->localeRouteService->getAllLocalizedPaths($currentRouteName);
    }
}
