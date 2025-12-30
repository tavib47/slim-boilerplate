<?php

declare(strict_types=1);

return [
    // Default locale (unprefixed routes)
    'default_locale' => $_ENV['APP_LOCALE'] ?? 'en',

    // Supported locales
    'supported_locales' => array_filter(
        array_map('trim', explode(',', $_ENV['APP_LOCALES'] ?? 'en')),
        static fn (string $locale): bool => $locale !== ''
    ),

    // Fallback chain (source strings are always English)
    'fallback_locales' => ['en'],

    // Path to translation files
    'translations_path' => dirname(__DIR__) . '/translations',

    // Route slug translations: route_name => [locale => translated_slug]
    // Default locale uses the original route pattern (no entry needed)
    'route_slugs' => [
        'about' => [
            'ro' => 'despre-noi',
        ],
        'privacy' => [
            'ro' => 'confidentialitate',
        ],
        'contact' => [
            'ro' => 'contact',
        ],
    ],
];
