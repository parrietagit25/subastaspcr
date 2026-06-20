<?php

require_once __DIR__ . '/env.php';

use PHPMailer\PHPMailer\PHPMailer;

function configure_mailer(PHPMailer $mail, ?string $fromName = null): void
{
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['MAIL_USERNAME'];
    $mail->Password = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
    $mail->Port = (int) $_ENV['MAIL_PORT'];
    $mail->CharSet = 'UTF-8';

    $mail->setFrom(
        $_ENV['MAIL_FROM_ADDRESS'],
        $fromName ?? $_ENV['MAIL_FROM_NAME']
    );
}
