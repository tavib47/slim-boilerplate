<?php

declare(strict_types=1);

use App\Middleware\LocaleMiddleware;
use App\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return static function (App $app): void {
    $app->add(SessionMiddleware::class);
    $app->add(LocaleMiddleware::class);
    $app->add(TwigMiddleware::createFromContainer($app, Twig::class));
    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();

    $settings = $app->getContainer()->get('settings');
    $displayErrorDetails = $settings['app']['debug'];

    $app->addErrorMiddleware($displayErrorDetails, true, true);
};
