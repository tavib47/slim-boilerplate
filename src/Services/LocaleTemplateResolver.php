<?php

declare(strict_types=1);

namespace App\Services;

class LocaleTemplateResolver
{
    public function __construct(
        private readonly string $templatesPath
    ) {
    }

    /**
     * Resolve template path, checking for locale-specific override first.
     *
     * For locale 'ro' and template 'pages/about.twig':
     * 1. Check: templates/pages/ro/about.twig
     * 2. Fallback: templates/pages/about.twig
     */
    public function resolve(string $template, string $locale, string $defaultLocale): string
    {
        if ($locale !== $defaultLocale) {
            $parts = pathinfo($template);
            $dirname = $parts['dirname'] ?? '';
            $basename = $parts['basename'] ?? $template;

            $localeTemplate = $dirname !== '.' && $dirname !== ''
                ? $dirname . '/' . $locale . '/' . $basename
                : $locale . '/' . $basename;

            $fullPath = $this->templatesPath . '/' . $localeTemplate;
            if (file_exists($fullPath)) {
                return $localeTemplate;
            }
        }

        return $template;
    }
}
