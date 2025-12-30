<?php

declare(strict_types=1);

use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use Slim\App;

return function (App $app): void {
    $app->get('/', [HomeController::class, 'index'])->setName('home');
    $app->get('/about', [PageController::class, 'about'])->setName('about');
    $app->get('/privacy', [PageController::class, 'privacy'])->setName('privacy');

    $app->get('/contact', [ContactController::class, 'show'])->setName('contact');
    $app->post('/contact', [ContactController::class, 'submit'])->setName('contact.submit');
};
