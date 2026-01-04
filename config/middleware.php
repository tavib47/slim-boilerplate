<?php

declare(strict_types=1);

use App\Handlers\HttpErrorHandler;
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
    $displayErrorDetails = $settings['app']['debug'] ?? false;

    $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);

    $errorHandler = new HttpErrorHandler(
        $app->getCallableResolver(),
        $app->getResponseFactory(),
    );
    $errorHandler->setContainer($app->getContainer());
    $errorMiddleware->setDefaultErrorHandler($errorHandler);
};
