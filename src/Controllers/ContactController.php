<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\SessionMiddleware;
use App\Services\MailService;
use App\Services\TranslationService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class ContactController
{
    public function __construct(
        private readonly MailService $mailService,
        private readonly TranslationService $translator,
        private readonly array $settings
    ) {
    }

    public function submit(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $message = trim($data['message'] ?? '');

        $errors = [];

        if (empty($name)) {
            $errors[] = $this->translator->trans('Name is required.');
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = $this->translator->trans('A valid email address is required.');
        }

        if (empty($message)) {
            $errors[] = $this->translator->trans('Message is required.');
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                SessionMiddleware::flash('error', $error);
            }
            SessionMiddleware::setFormData([
                'name' => $name,
                'email' => $email,
                'message' => $message,
            ]);
            return $this->redirectToRoute($request, $response, 'contact');
        }

        $subject = 'Contact Form Submission from ' . $name;
        $body = $this->formatEmailBody($name, $email, $message);
        $recipientEmail = $this->settings['contact']['email'];

        $sent = $this->mailService->send($recipientEmail, $subject, $body, $email);

        if ($sent) {
            $successMessage = $this->translator->trans('Thank you for your message. We will get back to you soon.');
            SessionMiddleware::flash('success', $successMessage);
        } else {
            SessionMiddleware::setFormData([
              'name' => $name,
              'email' => $email,
              'message' => $message,
            ]);
            $errorMessage = $this->translator->trans('Failed to send message. Please try again later.');
            SessionMiddleware::flash('error', $errorMessage);
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
        $locale = $request->getAttribute('locale', 'en');
        $defaultLocale = $request->getAttribute('default_locale', 'en');

        // Use locale-suffixed route name for non-default locales
        if ($locale !== $defaultLocale) {
            $routeName .= '.' . $locale;
        }

        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        $url = $routeParser->urlFor($routeName);

        return $response
            ->withHeader('Location', $url)
            ->withStatus(302);
    }
}
