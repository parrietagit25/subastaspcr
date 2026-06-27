<?php

require_once __DIR__ . '/env.php';

function resend_from(?string $fromName = null): string
{
    $fromAddress = resend_from_address();
    $defaultName = trim($_ENV['RESEND_FROM_NAME'] ?? 'Subastas Automarket', '"');
    $name = $fromName ?? $defaultName;

    return "{$name} <{$fromAddress}>";
}

function resend_from_address(): string
{
    return trim($_ENV['RESEND_FROM_ADDRESS'] ?? 'subastas@automarket.com.pa');
}

function mail_default_logos(): array
{
    $imgDir = dirname(__DIR__) . '/../img';
    $logos = [];

    $subastasLogo = $imgDir . '/logosubastas.png';
    if (is_readable($subastasLogo)) {
        $logos['logosubastas'] = $subastasLogo;
    }

    $pcrCandidates = glob($imgDir . '/logo20*.png') ?: [];
    if ($pcrCandidates !== []) {
        $logos['logogrupopcr'] = $pcrCandidates[0];
    }

    return $logos;
}

function build_resend_attachments(array $inlineImages): array
{
    $attachments = [];

    foreach ($inlineImages as $contentId => $path) {
        if (!is_readable($path)) {
            continue;
        }

        $attachments[] = [
            'filename' => basename($path),
            'content' => base64_encode((string) file_get_contents($path)),
            'content_id' => (string) $contentId,
        ];
    }

    return $attachments;
}

/**
 * @param array<int, string>|string $to
 * @param array<string, string> $inlineImages content_id => file path
 */
function send_email(
    array|string $to,
    string $subject,
    string $html,
    ?string $fromName = null,
    array $inlineImages = []
): array {
    $apiKey = trim($_ENV['RESEND_API_KEY'] ?? '');
    if ($apiKey === '') {
        return ['ok' => false, 'error' => 'RESEND_API_KEY no está configurada en .env'];
    }

    $recipients = is_array($to) ? array_values($to) : [$to];

    $payload = [
        'from' => resend_from($fromName),
        'to' => $recipients,
        'subject' => $subject,
        'html' => $html,
    ];

    $attachments = build_resend_attachments($inlineImages);
    if ($attachments !== []) {
        $payload['attachments'] = $attachments;
    }

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
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
        return [
            'ok' => true,
            'message' => 'Correo enviado correctamente',
            'id' => $data['id'] ?? null,
        ];
    }

    $error = $data['message'] ?? $data['error'] ?? $response;

    return ['ok' => false, 'error' => "Resend HTTP {$httpCode}: {$error}"];
}

function build_test_email_html(string $fromAddress): string
{
    $now = date('d/m/Y H:i:s');

    return '
        <html>
        <body style="font-family:Segoe UI,sans-serif;color:#1e293b;padding:20px;">
            <h2 style="color:#0f2744;">Correo de prueba - Subastas PCR</h2>
            <p>Este es un mensaje de prueba enviado desde el panel de administración.</p>
            <ul>
                <li><strong>Proveedor:</strong> Resend</li>
                <li><strong>Remitente:</strong> ' . htmlspecialchars($fromAddress) . '</li>
                <li><strong>Fecha:</strong> ' . htmlspecialchars($now) . '</li>
            </ul>
            <p style="color:#64748b;font-size:14px;">Si recibió este correo, la configuración funciona correctamente.</p>
        </body>
        </html>';
}

function send_test_email(string $to): array
{
    $result = send_email(
        $to,
        'Prueba Resend - Subastas PCR',
        build_test_email_html(resend_from_address())
    );

    if ($result['ok']) {
        $result['message'] = "Correo enviado a {$to} desde " . resend_from_address();
    }

    return $result;
}
