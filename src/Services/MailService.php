<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    public function __construct(
        private readonly array $settings
    ) {
    }

    public function send(string $to, string $subject, string $body, ?string $replyTo = null): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->settings['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->settings['username'];
            $mail->Password = $this->settings['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->settings['port'];

            $mail->setFrom($this->settings['from_address'], $this->settings['from_name']);
            $mail->addAddress($to);

            if ($replyTo) {
                $mail->addReplyTo($replyTo);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception) {
            return false;
        }
    }
}
