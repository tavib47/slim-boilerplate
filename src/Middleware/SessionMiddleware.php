<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $handler->handle($request);
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type][] = $message;
    }

    public static function getFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function setFormData(array $data): void
    {
        $_SESSION['form_data'] = $data;
    }

    /**
     * @return array<string, mixed>
     */
    public static function getFormData(): array
    {
        $data = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        return $data;
    }
}
