# Slim Framework Boilerplate

A modern PHP boilerplate built with Slim Framework 4 and Twig templating, pre-configured with DDEV for local development and code quality tools.

## Prerequisites

- [PHP](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download)
- [Docker](https://www.docker.com/)
- [DDEV](https://ddev.readthedocs.io/en/stable/)
- [mkcert](https://github.com/FiloSottile/mkcert)

## Quick Start

1. Create a project:
   ```bash
   composer create-project --ignore-platform-reqs tavib47/slim-boilerplate my-project
   cd my-project
   ```

2. Copy environment configuration:
   ```bash
   cp .env.example .env
   ```

3. Start DDEV and install dependencies:
   ```bash
   ./scripts/install.sh
   ```

4. Open in browser:
   ```bash
   ddev launch
   ```

## Project Setup

Before starting development, configure these files:

| File | Purpose |
|------|---------|
| `.env` | Environment variables (database, mail, app settings) |
| `.ddev/config.yaml` | DDEV project configuration |
| `composer.json` | Update project name and description |

## Features

| Feature | Description |
|---------|-------------|
| **Slim Framework 4** | Fast, minimal PHP micro-framework |
| **Twig 3** | Secure template engine with inheritance and components |
| **PHP-DI** | Autowiring dependency injection container |
| **Sass/SCSS** | Modular stylesheets with variables and partials |
| **Multilingual (i18n)** | URL-based locale detection, translated routes, YAML translations |
| **Session Management** | PHP sessions with flash messages |
| **Contact Form** | Ready-to-use form with SMTP email (PHPMailer) |
| **GDPR Cookie Banner** | Cookie consent with localStorage |
| **Environment Config** | `.env` file support via phpdotenv |
| **Code Quality** | PHPCS, PHPStan, pre-commit hooks |

## Configuration

All configuration is managed via environment variables in `.env`:

### Application
```env
APP_ENV=development      # Environment (development/production)
APP_DEBUG=true           # Enable debug mode and detailed errors
APP_URL=https://...      # Application URL
```

### Database
```env
DB_HOST=db               # Database host (use 'db' for DDEV)
DB_PORT=3306             # Database port
DB_DATABASE=db           # Database name
DB_USERNAME=db           # Database username
DB_PASSWORD=db           # Database password
```

### Mail (SMTP)
```env
MAIL_HOST=localhost      # SMTP host (use 'localhost' for DDEV MailHog)
MAIL_PORT=1025           # SMTP port (1025 for MailHog)
MAIL_USERNAME=           # SMTP username (empty for MailHog)
MAIL_PASSWORD=           # SMTP password (empty for MailHog)
MAIL_FROM_ADDRESS=...    # From email address
MAIL_FROM_NAME=...       # From name
CONTACT_EMAIL=...        # Contact form recipient
```

## Project Structure

```
├── config/                  # Configuration files
│   ├── container.php        # DI container setup
│   ├── middleware.php       # Middleware registration
│   ├── routes.php           # Route definitions (data-driven)
│   └── settings.php         # Application settings (including locale config)
├── public/                  # Web root (document root)
│   ├── css/style.css        # Compiled CSS (from scss/)
│   ├── js/                  # JavaScript files
│   └── index.php            # Application entry point
├── scss/                    # Sass source files
│   ├── style.scss           # Main entry point
│   ├── _variables.scss      # Design tokens
│   ├── _base.scss           # Reset and base styles
│   ├── _layout.scss         # Layout components
│   ├── _pages.scss          # Page-specific styles
│   └── components/          # Component partials
├── scripts/                 # Utility scripts
│   ├── code-qa.sh           # Code quality runner
│   └── utils.sh             # Shared utilities
├── src/                     # Application source code
│   ├── Controllers/         # Request handlers
│   ├── Middleware/          # HTTP middleware
│   └── Services/            # Business logic services
├── templates/               # Twig templates
│   ├── components/          # Reusable components
│   ├── layout/              # Base layouts
│   └── pages/               # Page-specific templates
├── var/                     # Runtime files
│   └── cache/               # Twig cache (gitignored)
├── .env.example             # Environment template
├── phpcs.xml.dist           # PHPCS configuration
└── phpstan.neon             # PHPStan configuration
```

## Development Workflow

### Starting and Stopping

```bash
ddev start          # Start the development environment
ddev stop           # Stop containers
ddev restart        # Restart containers
ddev launch         # Open site in browser
```

### Running Commands

```bash
ddev composer <cmd>     # Run Composer commands
ddev exec <cmd>         # Execute commands in the container
ddev ssh                # SSH into the web container
```

### Database Access

```bash
ddev mysql              # Open MySQL CLI
ddev sequelace          # Open Sequel Ace (macOS)
ddev tableplus          # Open TablePlus
```

### Adding New Pages

**For static pages** (using `PageController`), you only need two steps:

1. **Add a route** to the `$routes` array in `config/routes.php`:
   ```php
   'terms' => [
       'method' => 'GET',
       'path' => '/terms',
       'handler' => [PageController::class, 'show'],
   ],
   ```

2. **Create a template** in `templates/pages/terms.twig`:
   ```twig
   {% extends 'layout/base.twig' %}

   {% block title %}Terms of Service{% endblock %}

   {% block content %}
       <h1>Terms of Service</h1>
   {% endblock %}
   ```

Routes are automatically registered for all supported locales. To add translated URL slugs, see the [Multilingual Support](#multilingual-support-i18n) section.

**For pages with custom logic**, create a controller:

1. **Create a controller** in `src/Controllers/`:
   ```php
   class MyController
   {
       public function __construct(private readonly Twig $twig) {}

       public function index(Request $request, Response $response): Response
       {
           return $this->twig->render($response, 'pages/my-page.twig');
       }
   }
   ```

2. **Add a route** in `config/routes.php`:
   ```php
   'my-page' => [
       'method' => 'GET',
       'path' => '/my-page',
       'handler' => [MyController::class, 'index'],
   ],
   ```

3. **Create a template** in `templates/pages/my-page.twig`

## Theme Development (Sass/CSS)

Stylesheets are written in Sass and compiled to CSS. Source files are in `scss/`, compiled output goes to `public/css/style.css`.

### Commands

```bash
ddev composer theme:dev     # Compile (expanded, readable)
ddev composer theme:build   # Compile (minified, production)
ddev composer theme:watch   # Watch for changes (auto-recompile)
```

### File Structure

```
scss/
├── style.scss              # Main entry point
├── _variables.scss         # Colors, spacing, typography, breakpoints
├── _base.scss              # Reset, base styles, links
├── _layout.scss            # Header, nav, footer, container
├── _pages.scss             # Page-specific styles (hero, features)
└── components/             # Individual component styles
    ├── _index.scss         # Imports all components
    ├── _buttons.scss
    ├── _forms.scss
    ├── _flash-messages.scss
    ├── _feature-cards.scss
    └── _cookie-banner.scss
```

### Adding New Styles

1. **New component**: Create `scss/components/_my-component.scss`, add `@use 'my-component';` to `_index.scss`
2. **New page styles**: Add to `scss/_pages.scss` or create a new partial
3. **New variables**: Add to `scss/_variables.scss`

Each partial uses `@use '../variables' as *;` to access variables.

## Multilingual Support (i18n)

The boilerplate includes a complete internationalization system with URL-based locale detection and translated routes. All routes defined in `config/routes.php` are automatically registered for each supported locale.

### URL Structure

- Default locale has no prefix: `/about`, `/contact`
- Other locales are prefixed with translated slugs: `/ro/despre-noi`, `/fr/a-propos`

### Configuration

Configure languages in `config/settings.php` under the `locale` key:

```php
'locale' => [
    'default_locale' => 'en',
    'supported_locales' => ['en', 'ro', 'fr'],
    'fallback_locales' => ['en'],
    'translations_path' => dirname(__DIR__) . '/translations',

    // Translated URL slugs (keyed by route name)
    'route_slugs' => [
        'about' => [
            'ro' => 'despre-noi',
            'fr' => 'a-propos',
        ],
        'privacy' => [
            'ro' => 'confidentialitate',
            'fr' => 'confidentialite',
        ],
    ],
],
```

### Adding Route Translations

When you add a new route, you can optionally add translated URL slugs:

1. Add the route to `config/routes.php`:
   ```php
   'terms' => [
       'method' => 'GET',
       'path' => '/terms',
       'handler' => [PageController::class, 'show'],
   ],
   ```

2. Add translated slugs in `config/settings.php`:
   ```php
   'route_slugs' => [
       // ... existing slugs
       'terms' => [
           'ro' => 'termeni',
           'fr' => 'conditions',
       ],
   ],
   ```

This will generate:
- `/terms` (English - default, no prefix)
- `/ro/termeni` (Romanian)
- `/fr/conditions` (French)

### Translation Files

Translations are stored in YAML files in the `translations/` directory:

```yaml
# translations/messages.ro.yaml
"Home": "Acasă"
"About": "Despre noi"
"Contact": "Contact"
"Privacy policy": "Confidențialitate"
```

### Using Translations in Templates

```twig
{# Basic translation #}
<h1>{{ trans('pages.about.title') }}</h1>

{# Navigation with localized URLs #}
<a href="{{ route_localized('about', locale) }}">{{ trans('nav.about') }}</a>

{# Language switcher (automatically included in header) #}
{% include 'components/language-switcher.twig' %}
```

### Locale-Specific Template Overrides

You can override templates for specific locales by placing them in a locale subdirectory:

```
templates/pages/about.twig           # Default template (uses trans())
templates/pages/ro/about.twig        # Romanian override (completely custom)
```

The system checks for `pages/{locale}/template.twig` first, then falls back to `pages/template.twig`.

## Code Quality Tools

This boilerplate includes pre-configured code quality tools that run automatically on commit.

### Available Commands

```bash
./scripts/code-qa.sh    # Run all checks (PHPCS, PHPCBF, PHPStan)
ddev composer phpcs     # Run PHP CodeSniffer
ddev composer phpcbf    # Run PHP Code Beautifier (auto-fix)
ddev composer phpstan   # Run PHPStan static analysis
```

### Standards

- **PSR-12** - PHP coding standard
- **Slevomat Coding Standard** - Additional strict rules (type hints, unused code detection, etc.)
- **PHPStan Level 6** - Static analysis

### Git Hooks

Pre-commit hooks are automatically configured via `composer install`. They run all code quality checks before each commit.

```bash
ddev exec vendor/bin/cghooks update   # Update hooks
ddev exec vendor/bin/cghooks remove   # Remove hooks
```

## MailHog (Email Testing)

DDEV includes MailHog for capturing outgoing emails during development.

```bash
ddev launch -m           # Open MailHog interface
```

Or visit: `https://your-project.ddev.site:8026`

## License

MIT
