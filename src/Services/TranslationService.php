<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

class TranslationService
{
    private Translator $translator;
    private string $currentLocale;
    private string $defaultLocale;
    /** @var list<string> */
    private array $supportedLocales;

    /**
     * @param array{
     *     default_locale: string,
     *     supported_locales: list<string>,
     *     fallback_locales: list<string>,
     *     translations_path: string
     * } $config
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

    public function setLocale(string $locale): void
    {
        if (in_array($locale, $this->supportedLocales, true)) {
            $this->currentLocale = $locale;
            $this->translator->setLocale($locale);
        }
    }

    public function getLocale(): string
    {
        return $this->currentLocale;
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

    /**
     * @param array<string, string> $parameters
     */
    public function trans(string $id, array $parameters = [], ?string $domain = null): string
    {
        return $this->translator->trans($id, $parameters, $domain ?? 'messages');
    }
}
