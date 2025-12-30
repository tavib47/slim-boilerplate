<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Services\MailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class ContactController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly MailService $mailService,
        private readonly array $settings
    ) {
    }

    public function show(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'pages/contact.twig', [
            'flash' => SessionMiddleware::getFlash(),
        ]);
    }

    public function submit(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $message = trim($data['message'] ?? '');

        $errors = [];

        if (empty($name)) {
            $errors[] = 'Name is required.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        }

        if (empty($message)) {
            $errors[] = 'Message is required.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                SessionMiddleware::flash('error', $error);
            }
            return $this->redirectToRoute($request, $response, 'contact');
        }

        $subject = 'Contact Form Submission from ' . $name;
        $body = $this->formatEmailBody($name, $email, $message);
        $recipientEmail = $this->settings['contact']['email'];

        $sent = $this->mailService->send($recipientEmail, $subject, $body, $email);

        if ($sent) {
            SessionMiddleware::flash('success', 'Thank you for your message. We will get back to you soon.');
        } else {
            SessionMiddleware::flash('error', 'Failed to send message. Please try again later.');
        }

        return $this->redirectToRoute($request, $response, 'contact');
    }

    private function formatEmailBody(string $name, string $email, string $message): string
    {
        return sprintf(
            '<h2>Contact Form Submission</h2>
            <p><strong>Name:</strong> %s</p>
            <p><strong>Email:</strong> %s</p>
            <p><strong>Message:</strong></p>
            <p>%s</p>',
            htmlspecialchars($name),
            htmlspecialchars($email),
            nl2br(htmlspecialchars($message))
        );
    }

    private function redirectToRoute(Request $request, Response $response, string $routeName): Response
    {
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor($routeName);

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
}
