# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/claude-code) when working with this repository.

## Project Overview

This is a **Slim Framework 4 boilerplate** with Twig templating, designed for small to medium PHP projects. It uses DDEV for local development and includes pre-configured code quality tools.

## Tech Stack

- **PHP 8.4+**
- **Slim Framework 4** - Micro framework
- **Twig 3** - Template engine
- **PHP-DI** - Dependency injection container
- **Symfony Translation** - i18n with YAML files
- **PHPMailer** - SMTP email
- **DDEV** - Local development environment

## Development Environment

This project uses DDEV. All commands should be run through DDEV:

```bash
ddev start              # Start environment
ddev composer <cmd>     # Run Composer
ddev exec <cmd>         # Execute commands in container
ddev launch             # Open in browser
ddev mysql              # Database CLI
```

## Theme Development (Sass/CSS)

Styles are written in Sass. Source files in `scss/`, output to `public/css/style.css`.

```bash
ddev composer theme:dev     # Compile (expanded)
ddev composer theme:build   # Compile (minified)
ddev composer theme:watch   # Watch mode
```

### Sass Structure

```
scss/
├── style.scss          # Main entry (imports all partials)
├── _variables.scss     # Colors, spacing, breakpoints, typography
├── _base.scss          # Reset, base styles
├── _layout.scss        # Header, nav, footer, container
├── _pages.scss         # Page-specific (hero, features, page-content)
└── components/         # One file per component
    ├── _index.scss     # Imports all components
    ├── _buttons.scss
    ├── _forms.scss
    ├── _flash-messages.scss
    ├── _feature-cards.scss
    └── _cookie-banner.scss
```

### Adding Styles

- **New component**: Create `scss/components/_name.scss`, add `@use 'name';` to `components/_index.scss`
- **Variables**: Each partial imports variables with `@use '../variables' as *;`
- **Never edit** `public/css/style.css` directly - it's compiled output

## Code Quality Commands

```bash
# Run all checks (PHPCS, PHPCBF, PHPStan)
./scripts/code-qa.sh

# Individual tools
ddev composer phpcs     # Check code style
ddev composer phpcbf    # Auto-fix code style
ddev composer phpstan   # Static analysis
```

### Standards

- **PSR-12** with **Slevomat Coding Standard** (strict type hints, unused code detection)
- **PHPStan Level 6**
- Pre-commit hooks run automatically via `brainmaestro/composer-git-hooks`

## Project Structure

```
config/
├── container.php       # DI container definitions
├── locales.php         # Locale config (languages, route slugs)
├── middleware.php      # Middleware stack
├── routes.php          # Route definitions
└── settings.php        # App configuration (loads .env)

src/
├── Controllers/
│   ├── HomeController.php      # Homepage
│   ├── PageController.php      # Static pages (generic)
│   └── ContactController.php   # Contact form submission
├── Middleware/
│   ├── LocaleMiddleware.php    # URL locale detection
│   └── SessionMiddleware.php   # Sessions + flash messages
├── Services/
│   ├── LocaleRouteService.php      # Route-to-slug mapping
│   ├── LocaleTemplateResolver.php  # Locale-specific templates
│   ├── MailService.php             # PHPMailer wrapper
│   └── TranslationService.php      # Symfony translation wrapper
└── Twig/
    └── TranslationExtension.php    # trans(), route_localized() functions

templates/
├── layouts/base.twig           # Main layout
├── components/                 # Reusable (header, footer, nav, language-switcher, etc.)
└── pages/                      # Page templates (home, about, contact, privacy)
    └── {locale}/               # Optional locale-specific overrides

translations/
├── messages.en.yaml            # English translations
└── messages.ro.yaml            # Romanian translations

public/
├── index.php                   # Entry point
├── css/style.css               # Compiled CSS (do not edit directly)
└── js/app.js                   # Cookie consent JS

scss/                           # Sass source files
├── style.scss                  # Main entry point
├── _variables.scss             # Design tokens
├── _base.scss, _layout.scss, _pages.scss
└── components/                 # Component partials
```

## Key Patterns

### Static Pages

`PageController::show()` handles all static pages. The template is derived from the URL path:

```php
// routes.php - just add the route
$app->get('/terms', [PageController::class, 'show'])->setName('terms');

// Create templates/pages/terms.twig - done!
```

### Adding Routes

Routes are defined in `config/routes.php`:

```php
// Static page
$app->get('/my-page', [PageController::class, 'show'])->setName('my-page');

// With controller method
$app->get('/custom', [MyController::class, 'index'])->setName('custom');
$app->post('/custom', [MyController::class, 'submit'])->setName('custom.submit');
```

### Dependency Injection

Services are registered in `config/container.php`. PHP-DI autowires most dependencies, but complex services need explicit registration:

```php
$container->set(MyService::class, static function (ContainerInterface $c): MyService {
    return new MyService($c->get('settings')['my_config']);
});
```

### Flash Messages

```php
// Set flash message
SessionMiddleware::flash('success', 'Operation completed.');
SessionMiddleware::flash('error', 'Something went wrong.');

// Get and clear flash messages (in controller)
$flash = SessionMiddleware::getFlash();
```

### Twig Templates

Templates extend the base layout:

```twig
{% extends 'layouts/base.twig' %}

{% block title %}Page Title{% endblock %}
{% block meta_description %}SEO description{% endblock %}

{% block content %}
    <h1>Content here</h1>
{% endblock %}
```

Include components:

```twig
{% include 'components/header.twig' %}
```

## Configuration

All config is in `.env` (copy from `.env.example`):

- `APP_ENV` - development/production
- `APP_DEBUG` - true/false (controls error display, Twig cache)
- `APP_LOCALE` - Default locale (unprefixed routes)
- `APP_LOCALES` - Comma-separated supported locales (e.g., `en,ro,de`)
- `DB_*` - Database connection (use `db` as host for DDEV)
- `MAIL_*` - SMTP settings (use `localhost:1025` for DDEV MailHog)
- `CONTACT_EMAIL` - Contact form recipient

## Testing Email

DDEV includes MailHog. View captured emails:

```bash
ddev launch -m
```

## File Naming Conventions

- Controllers: `PascalCase` + `Controller` suffix (e.g., `PageController.php`)
- Services: `PascalCase` + `Service` suffix (e.g., `MailService.php`)
- Middleware: `PascalCase` + `Middleware` suffix
- Templates: `kebab-case.twig` (e.g., `cookie-banner.twig`)
- Sass partials: `_kebab-case.scss` (e.g., `_cookie-banner.scss`)
- Config files: `lowercase.php`

## Multilingual System (i18n)

### URL Structure
- Default locale: `/about`, `/contact` (no prefix)
- Other locales: `/ro/despre-noi`, `/ro/contact` (prefixed + translated slugs)

### Adding Route Translations

Edit `config/locales.php`:

```php
'route_slugs' => [
    'about' => [
        'ro' => 'despre-noi',
        'de' => 'uber-uns',
    ],
],
```

### Adding a New Language

1. Add locale to `APP_LOCALES` in `.env`: `en,ro,de`
2. Add route slug translations in `config/locales.php`
3. Create translation file: `translations/messages.de.yaml`

### Twig Functions

```twig
{{ trans('key.path') }}                    {# Translate string #}
{{ route_localized('about', 'ro') }}       {# Get /ro/despre-noi #}
{{ language_switcher_urls('about') }}      {# Get all locale URLs for route #}
```

### Global Twig Variables

- `locale` - Current locale (e.g., `ro`)
- `default_locale` - Default locale (e.g., `en`)
- `supported_locales` - Array of all locales

### Template Override by Locale

Create `templates/pages/{locale}/template.twig` to override for specific locale:
- `templates/pages/about.twig` - Default (uses `trans()`)
- `templates/pages/ro/about.twig` - Romanian override (completely custom)

## Important Notes

1. **Always run code-qa before committing** - pre-commit hooks enforce this
2. **Twig cache** is disabled in development (`APP_DEBUG=true`)
3. **PDO connection** is lazy-loaded - only connects when used
4. **Static closures** are enforced in config files for performance
5. **Route group callbacks** cannot be static (Slim requirement) - use phpcs:ignore comment
