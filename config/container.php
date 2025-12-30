<?php

declare(strict_types=1);

use App\Controllers\ContactController;
use App\Services\MailService;
use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;

return function (Container $container, array $settings): void {
    $container->set('settings', $settings);

    $container->set(Twig::class, function (ContainerInterface $c): Twig {
        $settings = $c->get('settings');
        $debug = $settings['app']['debug'];

        return Twig::create($settings['twig']['path'], [
            'cache' => $debug ? false : $settings['twig']['cache'],
            'debug' => $debug,
        ]);
    });

    $container->set(PDO::class, function (ContainerInterface $c): PDO {
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

    $container->set(MailService::class, function (ContainerInterface $c): MailService {
        $settings = $c->get('settings')['mail'];
        return new MailService($settings);
    });

    $container->set(ContactController::class, function (ContainerInterface $c): ContactController {
        return new ContactController(
            $c->get(Twig::class),
            $c->get(MailService::class),
            $c->get('settings')
        );
    });
};
