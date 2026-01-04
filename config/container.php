<?php

declare(strict_types=1);

use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Middleware\LocaleMiddleware;
use App\Services\LocaleRouteService;
use App\Services\LocaleTemplateResolver;
use App\Services\MailService;
use App\Services\TranslationService;
use App\Twig\TranslationExtension;
use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;

return static function (Container $container, array $settings): void {
    $container->set('settings', $settings);

    $container->set(
        TranslationService::class,
        static fn (ContainerInterface $c): TranslationService => new TranslationService($c->get('settings')['locale']),
    );

    $container->set(
        LocaleRouteService::class,
        static fn (ContainerInterface $c): LocaleRouteService => new LocaleRouteService($c->get('settings')['locale']),
    );

    $container->set(
        LocaleTemplateResolver::class,
        static fn (ContainerInterface $c): LocaleTemplateResolver => new LocaleTemplateResolver(
            $c->get('settings')['twig']['path'],
        ),
    );

    $container->set(
        LocaleMiddleware::class,
        static fn (ContainerInterface $c): LocaleMiddleware => new LocaleMiddleware(
            $c->get(TranslationService::class),
            $c->get(LocaleRouteService::class),
        ),
    );

    $container->set(Twig::class, static function (ContainerInterface $c): Twig {
        $settings = $c->get('settings');
        $debug = $settings['app']['debug'];

        $twig = Twig::create($settings['twig']['path'], [
            'cache' => $debug ? false : $settings['twig']['cache'],
            'debug' => $debug,
        ]);

        $twig->addExtension(new TranslationExtension(
            $c->get(TranslationService::class),
            $c->get(RouteParserInterface::class),
        ));

        return $twig;
    });

    $container->set(PDO::class, static function (ContainerInterface $c): PDO {
        $settings = $c->get('settings')['database'];

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $settings['host'],
            $settings['port'],
            $settings['database'],
            $settings['charset'],
        );

        return new PDO($dsn, $settings['username'], $settings['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    });

    $container->set(MailService::class, static function (ContainerInterface $c): MailService {
        $settings = $c->get('settings')['mail'];

        return new MailService($settings, $c->get(Twig::class));
    });

    $container->set(HomeController::class, static fn (ContainerInterface $c): HomeController => new HomeController(
        $c->get(Twig::class),
        $c->get(LocaleTemplateResolver::class),
    ));

    $container->set(PageController::class, static fn (ContainerInterface $c): PageController => new PageController(
        $c->get(Twig::class),
        $c->get(LocaleTemplateResolver::class),
    ));

    $container->set(
        ContactController::class,
        static fn (ContainerInterface $c): ContactController => new ContactController(
            $c->get(MailService::class),
            $c->get(TranslationService::class),
            $c->get('settings'),
        ),
    );
};
