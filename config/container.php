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
use Slim\Views\Twig;

return static function (Container $container, array $settings): void {
    $container->set('settings', $settings);

    $container->set(TranslationService::class, static function (ContainerInterface $c): TranslationService {
        return new TranslationService($c->get('settings')['locale']);
    });

    $container->set(LocaleRouteService::class, static function (ContainerInterface $c): LocaleRouteService {
        return new LocaleRouteService($c->get('settings')['locale']);
    });

    $container->set(LocaleTemplateResolver::class, static function (ContainerInterface $c): LocaleTemplateResolver {
        return new LocaleTemplateResolver($c->get('settings')['twig']['path']);
    });

    $container->set(LocaleMiddleware::class, static function (ContainerInterface $c): LocaleMiddleware {
        return new LocaleMiddleware(
            $c->get(TranslationService::class),
            $c->get(LocaleRouteService::class)
        );
    });

    $container->set(Twig::class, static function (ContainerInterface $c): Twig {
        $settings = $c->get('settings');
        $debug = $settings['app']['debug'];

        $twig = Twig::create($settings['twig']['path'], [
            'cache' => $debug ? false : $settings['twig']['cache'],
            'debug' => $debug,
        ]);

        $twig->addExtension(new TranslationExtension(
            $c->get(TranslationService::class),
            $c->get(LocaleRouteService::class)
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
            $settings['charset']
        );

        return new PDO($dsn, $settings['username'], $settings['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    });

    $container->set(MailService::class, static function (ContainerInterface $c): MailService {
        $settings = $c->get('settings')['mail'];
        return new MailService($settings);
    });

    $container->set(HomeController::class, static function (ContainerInterface $c): HomeController {
        return new HomeController(
            $c->get(Twig::class),
            $c->get(LocaleTemplateResolver::class)
        );
    });

    $container->set(PageController::class, static function (ContainerInterface $c): PageController {
        return new PageController(
            $c->get(Twig::class),
            $c->get(LocaleTemplateResolver::class)
        );
    });

    $container->set(ContactController::class, static function (ContainerInterface $c): ContactController {
        return new ContactController(
            $c->get(MailService::class),
            $c->get('settings')
        );
    });
};
