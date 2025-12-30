<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    'app' => [
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    ],
    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'database' => $_ENV['DB_DATABASE'] ?? '',
        'username' => $_ENV['DB_USERNAME'] ?? '',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
    ],
    'mail' => [
        'host' => $_ENV['MAIL_HOST'] ?? '',
        'port' => (int) ($_ENV['MAIL_PORT'] ?? 587),
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? '',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? '',
    ],
    'contact' => [
        'email' => $_ENV['CONTACT_EMAIL'] ?? '',
    ],
    'twig' => [
        'path' => dirname(__DIR__) . '/templates',
        'cache' => dirname(__DIR__) . '/var/cache',
    ],
    'locale' => [
      'default_locale' => $_ENV['APP_LOCALE'] ?? 'en',
      'supported_locales' => array_filter(
          array_map('trim', explode(',', $_ENV['APP_LOCALES'] ?? 'en')),
          static fn (string $locale): bool => $locale !== ''
      ),
      'fallback_locales' => ['en'],
      'translations_path' => dirname(__DIR__) . '/translations',
      'route_slugs' => [
        'about' => [
          'ro' => 'despre-noi',
          'fr' => 'a-propos',
          'es' => 'acerca-de',
        ],
        'privacy' => [
          'ro' => 'confidentialitate',
          'fr' => 'confidentialite',
          'es' => 'privacidad',
        ],
        'contact' => [
          'ro' => 'contact',
          'fr' => 'contact',
          'es' => 'contacto',
        ],
      ],
    ],
];
