<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

if (($_SESSION['tipo_user'] ?? '') !== 'admin') {
    http_response_code(403);
    die('Acceso restringido a administradores.');
}

require 'vendor/autoload.php';
require_once 'config/mail.php';

$mensaje = '';
$tipoAlerta = 'success';
$destinatario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinatario = trim($_POST['destinatario'] ?? '');
    $metodo = $_POST['metodo'] ?? '';

    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Ingrese un correo electrónico válido.';
        $tipoAlerta = 'danger';
    } elseif ($metodo === 'smtp') {
        $resultado = send_test_email_smtp($destinatario);
        $mensaje = $resultado['ok'] ? $resultado['message'] : $resultado['error'];
        $tipoAlerta = $resultado['ok'] ? 'success' : 'danger';
    } elseif ($metodo === 'resend') {
        $resultado = send_test_email_resend($destinatario);
        $mensaje = $resultado['ok'] ? $resultado['message'] : $resultado['error'];
        $tipoAlerta = $resultado['ok'] ? 'success' : 'danger';
    } else {
        $mensaje = 'Seleccione un método de envío válido.';
        $tipoAlerta = 'danger';
    }
}

$smtpFrom = $_ENV['MAIL_FROM_ADDRESS'] ?? '—';
$resendFrom = $_ENV['RESEND_FROM_ADDRESS'] ?? 'subastas@automarket.com.pa';
$resendConfigured = trim($_ENV['RESEND_API_KEY'] ?? '') !== '';

$pageTitle = 'Test de correo - Subastas PCR';
include('includes/admin_layout_open.php');
?>

<main class="admin-main">
    <div class="page-header">
        <h1><i class="bi bi-envelope-check me-2"></i>Test de envío de correo</h1>
        <p>Pruebe SMTP (Outlook) y Resend con el mismo destinatario</p>
    </div>

    <?php if ($mensaje): ?>
    <div class="alert alert-<?php echo htmlspecialchars($tipoAlerta); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($mensaje); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="form-card">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="destinatario" class="form-label">Correo de destino</label>
                        <input
                            type="email"
                            class="form-control"
                            id="destinatario"
                            name="destinatario"
                            value="<?php echo htmlspecialchars($destinatario); ?>"
                            placeholder="ejemplo@correo.com"
                            required
                        >
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" name="metodo" value="smtp" class="btn btn-pcr-primary">
                            <i class="bi bi-send me-1"></i> Enviar prueba SMTP
                        </button>
                        <button type="submit" name="metodo" value="resend" class="btn btn-outline-primary" <?php echo $resendConfigured ? '' : 'disabled'; ?>>
                            <i class="bi bi-lightning me-1"></i> Enviar prueba Resend
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="form-card">
                <h2 class="h5 mb-3">Configuración actual</h2>

                <div class="mb-3">
                    <strong class="d-block text-muted small text-uppercase">SMTP (normal)</strong>
                    <span><?php echo htmlspecialchars($smtpFrom); ?></span>
                    <div class="small text-muted">vía <?php echo htmlspecialchars($_ENV['MAIL_HOST'] ?? '—'); ?></div>
                </div>

                <div class="mb-3">
                    <strong class="d-block text-muted small text-uppercase">Resend</strong>
                    <span><?php echo htmlspecialchars($resendFrom); ?></span>
                    <?php if ($resendConfigured): ?>
                        <div class="small text-success"><i class="bi bi-check-circle me-1"></i>API key configurada</div>
                    <?php else: ?>
                        <div class="small text-danger"><i class="bi bi-exclamation-circle me-1"></i>Falta RESEND_API_KEY en .env</div>
                    <?php endif; ?>
                </div>

                <p class="small text-muted mb-0">
                    Agregue en <code>.env</code>: <code>RESEND_API_KEY=re_...</code>
                </p>
            </div>
        </div>
    </div>
</main>

<?php include('includes/admin_layout_close.php'); ?>
