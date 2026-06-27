<nav class="navbar navbar-expand-lg navbar-dark pcr-navbar fixed-top" aria-label="Navegación principal">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="inicio.php">
            <img src="../img/logosubastas.png" alt="Grupo PCR Subastas">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin" aria-controls="navAdmin" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navAdmin">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="inicio.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="main.php"><i class="bi bi-inbox me-1"></i> Solicitudes</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-folder2-open me-1"></i> Registros
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="main.php"><i class="bi bi-file-earmark-text me-2"></i>Solicitudes</a></li>
                        <li><a class="dropdown-item" href="aprobados.php"><i class="bi bi-check-circle me-2"></i>Aprobados</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="notificacion.php"><i class="bi bi-envelope me-2"></i>Notificaciones email</a></li>
                        <li><a class="dropdown-item" href="notificaciones_sms.php"><i class="bi bi-chat-dots me-2"></i>Notificaciones SMS</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear me-1"></i> Mantenimiento
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="usuarios.php"><i class="bi bi-people me-2"></i>Usuarios</a></li>
                        <?php if (($_SESSION['tipo_user'] ?? '') === 'admin'): ?>
                        <li><a class="dropdown-item" href="test_correo.php"><i class="bi bi-envelope-check me-2"></i>Test de correo</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <?php if (isset($_SESSION['email'])): ?>
                <span class="pcr-nav-user d-none d-md-inline">
                    <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($_SESSION['email']); ?>
                </span>
                <?php endif; ?>
                <a class="btn btn-sm btn-outline-light" href="salir.php"><i class="bi bi-box-arrow-right me-1"></i> Salir</a>
            </div>
        </div>
    </div>
</nav>
