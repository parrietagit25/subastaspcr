<?php

require_once __DIR__ . '/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

function build_test_email_html(string $provider, string $fromAddress): string
{
    $now = date('d/m/Y H:i:s');
    return '
        <html>
        <body style="font-family:Segoe UI,sans-serif;color:#1e293b;padding:20px;">
            <h2 style="color:#0f2744;">Correo de prueba - Subastas PCR</h2>
            <p>Este es un mensaje de prueba enviado desde el panel de administración.</p>
            <ul>
                <li><strong>Proveedor:</strong> ' . htmlspecialchars($provider) . '</li>
                <li><strong>Remitente:</strong> ' . htmlspecialchars($fromAddress) . '</li>
                <li><strong>Fecha:</strong> ' . htmlspecialchars($now) . '</li>
            </ul>
            <p style="color:#64748b;font-size:14px;">Si recibió este correo, la configuración funciona correctamente.</p>
        </body>
        </html>';
}

function send_test_email_smtp(string $to): array
{
    $mail = new PHPMailer(true);
    $fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? '';

    try {
        configure_mailer($mail);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Prueba SMTP - Subastas PCR';
        $mail->Body = build_test_email_html('SMTP (Outlook/PHPMailer)', $fromAddress);
        $mail->send();

        return [
            'ok' => true,
            'message' => "Correo SMTP enviado a {$to} desde {$fromAddress}",
        ];
    } catch (Exception $e) {
        return [
            'ok' => false,
            'error' => $mail->ErrorInfo ?: $e->getMessage(),
        ];
    }
}

function send_test_email_resend(string $to): array
{
    $apiKey = trim($_ENV['RESEND_API_KEY'] ?? '');
    if ($apiKey === '') {
        return ['ok' => false, 'error' => 'RESEND_API_KEY no está configurada en .env'];
    }

    $fromAddress = trim($_ENV['RESEND_FROM_ADDRESS'] ?? 'subastas@automarket.com.pa');
    $fromName = trim($_ENV['RESEND_FROM_NAME'] ?? 'Subastas Automarket', '"');
    $from = "{$fromName} <{$fromAddress}>";

    $payload = json_encode([
        'from' => $from,
        'to' => [$to],
        'subject' => 'Prueba Resend - Subastas PCR',
        'html' => build_test_email_html('Resend API', $fromAddress),
    ]);

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['ok' => false, 'error' => 'Error de conexión con Resend: ' . $curlError];
    }

    $data = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300) {
        $id = $data['id'] ?? 'sin-id';
        return [
            'ok' => true,
            'message' => "Correo Resend enviado a {$to} desde {$fromAddress} (id: {$id})",
        ];
    }

    $error = $data['message'] ?? $data['error'] ?? $response;
    return ['ok' => false, 'error' => "Resend HTTP {$httpCode}: {$error}"];
}
