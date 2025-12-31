<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    'app' => [
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'env' => $_ENV['APP_ENV'] ?? 'production',
        'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    ],
    'contact' => [
        'email' => $_ENV['CONTACT_EMAIL'] ?? '',
    ],
    'database' => [
        'charset' => 'utf8mb4',
        'database' => $_ENV['DB_DATABASE'] ?? '',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'username' => $_ENV['DB_USERNAME'] ?? '',
    ],
    'locale' => [
      'default_locale' => 'en',
      'fallback_locales' => ['en'],
      'route_slugs' => [
        'about' => [
          'es' => 'acerca-de',
          'fr' => 'a-propos',
          'ro' => 'despre-noi',
        ],
        'contact' => [
          'es' => 'contacto',
          'fr' => 'contact',
          'ro' => 'contact',
        ],
        'privacy' => [
          'es' => 'privacidad',
          'fr' => 'confidentialite',
          'ro' => 'confidentialitate',
        ],
      ],
      'supported_locales' => ['en', 'ro', 'fr', 'es'],
      'translations_path' => dirname(__DIR__) . '/translations',
    ],
    'mail' => [
        'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? '',
        'from_name' => $_ENV['MAIL_FROM_NAME'] ?? '',
        'host' => $_ENV['MAIL_HOST'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'port' => (int) ($_ENV['MAIL_PORT'] ?? 587),
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
    ],
    'twig' => [
        'cache' => dirname(__DIR__) . '/var/cache',
        'path' => dirname(__DIR__) . '/templates',
    ],
];
