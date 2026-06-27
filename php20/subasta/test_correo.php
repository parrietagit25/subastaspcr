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

    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Ingrese un correo electrónico válido.';
        $tipoAlerta = 'danger';
    } else {
        $resultado = send_test_email($destinatario);
        $mensaje = $resultado['ok'] ? $resultado['message'] : $resultado['error'];
        $tipoAlerta = $resultado['ok'] ? 'success' : 'danger';
    }
}

$resendFrom = resend_from_address();
$resendConfigured = trim($_ENV['RESEND_API_KEY'] ?? '') !== '';

$pageTitle = 'Test de correo - Subastas PCR';
include('includes/admin_layout_open.php');
?>

<main class="admin-main">
    <div class="page-header">
        <h1><i class="bi bi-envelope-check me-2"></i>Test de envío de correo</h1>
        <p>Envío de prueba vía Resend</p>
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
                            <?php echo $resendConfigured ? '' : 'disabled'; ?>
                        >
                    </div>

                    <button type="submit" class="btn btn-pcr-primary" <?php echo $resendConfigured ? '' : 'disabled'; ?>>
                        <i class="bi bi-send me-1"></i> Enviar prueba
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="form-card">
                <h2 class="h5 mb-3">Configuración Resend</h2>
                <div class="mb-3">
                    <strong class="d-block text-muted small text-uppercase">Remitente</strong>
                    <span><?php echo htmlspecialchars($resendFrom); ?></span>
                    <?php if ($resendConfigured): ?>
                        <div class="small text-success mt-1"><i class="bi bi-check-circle me-1"></i>API key configurada</div>
                    <?php else: ?>
                        <div class="small text-danger mt-1"><i class="bi bi-exclamation-circle me-1"></i>Falta RESEND_API_KEY en .env</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include('includes/admin_layout_close.php'); ?>
