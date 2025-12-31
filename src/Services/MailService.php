<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Slim\Views\Twig;

/**
 * Service for sending emails via SMTP using PHPMailer.
 */
class MailService
{
    /**
     * Creates a new MailService instance.
     *
     * @param array<string, mixed> $settings Mail configuration settings
     * @param Twig                 $twig     Twig view renderer for email templates
     */
    public function __construct(
        private readonly array $settings,
        private readonly Twig $twig,
    ) {
    }

    /**
     * Sends an email message.
     *
     * @param string      $to      Recipient email address
     * @param string      $subject Email subject
     * @param string      $body    Email body content
     * @param string|null $replyTo Optional reply-to email address
     *
     * @return bool True if email was sent successfully, false otherwise
     */
    public function send(string $to, string $subject, string $body, ?string $replyTo = null): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->settings['host'];
            $mail->Port = $this->settings['port'];

            // Only enable auth if credentials are provided
            if (!empty($this->settings['username'])) {
                $mail->SMTPAuth = true;
                $mail->Username = $this->settings['username'];
                $mail->Password = $this->settings['password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPAuth = false;
                $mail->SMTPSecure = '';
            }

            $mail->setFrom($this->settings['from_address'], $this->settings['from_name']);
            $mail->addAddress($to);

            if ($replyTo) {
                $mail->addReplyTo($replyTo);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;

            $htmlBody = $this->twig->fetch('emails/base.twig', ['body' => $body]);
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception) {
            return false;
        }
    }
}
