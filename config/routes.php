<?php

declare(strict_types=1);

use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use Slim\App;

return static function (App $app): void {
    $app->get('/', [HomeController::class, 'index'])->setName('home');

    // Static pages - template is derived from URL path (e.g., /about -> pages/about.twig)
    $app->get('/about', [PageController::class, 'show'])->setName('about');
    $app->get('/privacy', [PageController::class, 'show'])->setName('privacy');
    $app->get('/contact', [PageController::class, 'show'])->setName('contact');

    // Contact form submission
    $app->post('/contact', [ContactController::class, 'submit'])->setName('contact.submit');
};
