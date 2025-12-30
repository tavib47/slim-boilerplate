# Slim Framework Boilerplate

A modern PHP boilerplate built with Slim Framework 4 and Twig templating.

## Requirements

- PHP 8.4+
- Composer

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy the environment file and configure it:
   ```bash
   cp .env.example .env
   ```
4. Edit `.env` with your settings (database, SMTP, etc.)

## Development Server

Start the built-in PHP development server:

```bash
composer start
```

Then visit http://localhost:8080

## Project Structure

```
├── config/              # Configuration files
│   ├── container.php    # DI container setup
│   ├── middleware.php   # Middleware registration
│   ├── routes.php       # Route definitions
│   └── settings.php     # Application settings
├── public/              # Web root
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   └── index.php       # Entry point
├── src/                 # Application source code
│   ├── Controllers/    # Request handlers
│   ├── Middleware/     # HTTP middleware
│   └── Services/       # Business logic services
├── templates/           # Twig templates
│   ├── components/     # Reusable components
│   ├── layouts/        # Base layouts
│   └── pages/          # Page templates
└── var/                 # Runtime files (cache, logs)
```

## Features

- **Slim Framework 4** - Micro framework for PHP
- **Twig 3** - Template engine with component-based structure
- **PHP-DI** - Dependency injection container
- **Session Management** - With flash messages support
- **Contact Form** - SMTP email via PHPMailer
- **GDPR Cookie Banner** - Consent management
- **Environment Config** - Via `.env` files

## Configuration

Edit `.env` to configure:

- `APP_ENV` - Environment (development/production)
- `APP_DEBUG` - Enable debug mode
- `DB_*` - Database connection settings
- `MAIL_*` - SMTP email settings
- `CONTACT_EMAIL` - Contact form recipient

## Adding New Pages

1. Create a controller method or new controller in `src/Controllers/`
2. Add a route in `config/routes.php`
3. Create a template in `templates/pages/`

## License

MIT
