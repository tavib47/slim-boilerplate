<?php

declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

$settings = require dirname(__DIR__) . '/config/settings.php';

$container = new Container();
AppFactory::setContainer($container);

$containerSetup = require dirname(__DIR__) . '/config/container.php';
$containerSetup($container, $settings);

$app = AppFactory::create();

$middleware = require dirname(__DIR__) . '/config/middleware.php';
$middleware($app);

$routes = require dirname(__DIR__) . '/config/routes.php';
$routes($app);

$app->run();
