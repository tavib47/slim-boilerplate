<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware for managing sessions and flash messages.
 */
class SessionMiddleware implements MiddlewareInterface
{
    /**
     * Processes the request and ensures session is started.
     *
     * @param ServerRequestInterface  $request HTTP request
     * @param RequestHandlerInterface $handler Request handler
     *
     * @return ResponseInterface HTTP response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $handler->handle($request);
    }

    /**
     * Adds a flash message to the session.
     *
     * @param string $type    Message type (e.g., 'success', 'error')
     * @param string $message Message content
     *
     * @return void
     */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type][] = $message;
    }

    /**
     * Gets and clears all flash messages from the session.
     *
     * @return array<string, list<string>> Flash messages grouped by type
     */
    public static function getFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Stores form data in the session for repopulation after errors.
     *
     * @param array<string, mixed> $data Form field values
     *
     * @return void
     */
    public static function setFormData(array $data): void
    {
        $_SESSION['form_data'] = $data;
    }

    /**
     * Gets and clears stored form data from the session.
     *
     * @return array<string, mixed> Form field values
     */
    public static function getFormData(): array
    {
        $data = $_SESSION['form_data'] ?? [];
        unset($_SESSION['form_data']);
        return $data;
    }
}
