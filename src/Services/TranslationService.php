<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Service for managing translations using Symfony Translation component.
 */
class TranslationService
{
    /**
     * Symfony translator instance.
     */
    private Translator $translator;

    /**
     * Currently active locale.
     */
    private string $currentLocale;

    /**
     * Default application locale.
     */
    private string $defaultLocale;

    /**
     * Supported locale codes.
     *
     * @var list<string>
     */
    private array $supportedLocales;

    /**
     * Creates a new TranslationService instance.
     *
     * @param array<string, mixed> $config
     *   Translation configuration with keys:
     *   default_locale, supported_locales,
     *                                      fallback_locales, translations_path.
     */
    public function __construct(array $config)
    {
        $this->defaultLocale = $config['default_locale'];
        $this->currentLocale = $this->defaultLocale;
        $this->supportedLocales = $config['supported_locales'];

        $this->translator = new Translator($this->defaultLocale);
        $this->translator->addLoader('yaml', new YamlFileLoader());
        $this->translator->setFallbackLocales($config['fallback_locales']);

        foreach ($this->supportedLocales as $locale) {
            $file = $config['translations_path'] . "/messages.{$locale}.yaml";
            if (file_exists($file)) {
                $this->translator->addResource('yaml', $file, $locale);
            }
        }
    }

    /**
     * Sets the current locale for translations.
     *
     * @param string $locale
     *   Locale code to set.
     *
     * @return void
     */
    public function setLocale(string $locale): void
    {
        if (in_array($locale, $this->supportedLocales, true)) {
            $this->currentLocale = $locale;
            $this->translator->setLocale($locale);
        }
    }

    /**
     * Gets the currently active locale.
     *
     * @return string Current locale code
     */
    public function getLocale(): string
    {
        return $this->currentLocale;
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

    /**
     * Translates a message string.
     *
     * @param string $id
     *   Message identifier.
     * @param array<string, string> $parameters
     *   Placeholder replacements.
     * @param string|null $domain
     *   Translation domain.
     *
     * @return string Translated message
     */
    public function trans(string $id, array $parameters = [], ?string $domain = null): string
    {
        return $this->translator->trans($id, $parameters, $domain ?? 'messages');
    }
}
