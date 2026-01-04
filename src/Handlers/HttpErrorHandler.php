<?php

declare(strict_types=1);

namespace App\Handlers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Slim\Views\Twig;

/**
 * Custom error handler that renders styled Twig error pages.
 */
class HttpErrorHandler extends ErrorHandler
{
    /**
     * DI container for accessing Twig.
     */
    private ContainerInterface $container;

    /**
     * Set the DI container.
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * Render the error response.
     */
    protected function respond(): ResponseInterface
    {
        $exception = $this->exception;
        $statusCode = 500;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        }

        $response = $this->responseFactory->createResponse($statusCode);

        // Render styled error page
        $twig = $this->container->get(Twig::class);
        $template = $this->getErrorTemplate($statusCode);

        $context = [
            'status_code' => $statusCode,
            'message' => $exception->getMessage(),
        ];

        // Add debug info in development
        if ($this->displayErrorDetails) {
            $context['debug'] = [
                'exception' => $exception::class,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        return $twig->render($response, $template, $context);
    }

    /**
     * Get the appropriate error template for the status code.
     */
    private function getErrorTemplate(int $statusCode): string
    {
        $specific = "errors/{$statusCode}.twig";
        $templatePath = __DIR__ . '/../../templates/' . $specific;

        if (file_exists($templatePath)) {
            return $specific;
        }

        return 'errors/default.twig';
    }
}
