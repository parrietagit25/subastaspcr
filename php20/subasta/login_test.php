<?php
session_start();

$mensaje = '';
$pdo = null;
$errorDb = '';
$columnas = [];
$usuarios = [];

try {
    $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $errorDb = $e->getMessage();
}

if ($pdo) {
    try {
        $columnas = $pdo->query('SHOW COLUMNS FROM usuarios')->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $errorDb = $e->getMessage();
    }
}

function col_val(array $row, array $nombres, $default = '')
{
    foreach ($nombres as $nombre) {
        if (array_key_exists($nombre, $row) && $row[$nombre] !== null && $row[$nombre] !== '') {
            return $row[$nombre];
        }
    }
    return $default;
}

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    try {
        if ($accion === 'cambiar_password' && $id > 0) {
            $nueva = (string) ($_POST['nueva_password'] ?? '');
            if (strlen($nueva) < 4) {
                $mensaje = '<div class="alert alert-warning">La contraseña debe tener al menos 4 caracteres.</div>';
            } else {
                $hash = password_hash($nueva, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
                $stmt->execute([$hash, $id]);
                $mensaje = '<div class="alert alert-success">Contraseña actualizada para el usuario #' . $id . '. Ya puede entrar con esa clave en el login normal.</div>';
            }
        }

        if ($accion === 'entrar' && $id > 0) {
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = col_val($user, ['email', 'correo', 'usuario']);
                $_SESSION['tipo_user'] = col_val($user, ['tipo_user', 'tipo_usuario', 'rol'], 'admin');
                header('Location: main.php');
                exit();
            }
            $mensaje = '<div class="alert alert-danger">No se encontró el usuario.</div>';
        }
    } catch (PDOException $e) {
        $mensaje = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

if ($pdo) {
    try {
        $usuarios = $pdo->query('SELECT * FROM usuarios ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorDb = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login test - Subastas PCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/admin-corporate.css" rel="stylesheet">
  </head>
  <body class="admin-body" style="padding-top: 0;">
    <main class="admin-main">
      <div class="page-header">
        <h1><i class="bi bi-exclamation-triangle me-2"></i>Login test (temporal)</h1>
        <p>Lista de usuarios en la base y cambio de contraseña. Eliminar este archivo cuando el login funcione.</p>
      </div>

      <?php echo $mensaje; ?>

      <div class="form-card mb-4">
        <h2 class="h5 mb-3">Conexión a base de datos</h2>
        <?php if ($errorDb): ?>
          <div class="alert alert-danger mb-0">
            <strong>Error:</strong> <?php echo htmlspecialchars($errorDb); ?>
          </div>
        <?php else: ?>
          <p class="mb-1"><strong>Host:</strong> db &nbsp; <strong>BD:</strong> subastas &nbsp; <strong>Usuario MySQL:</strong> root</p>
          <p class="mb-1 text-success"><i class="bi bi-check-circle me-1"></i>Conexión OK. Usuarios encontrados: <strong><?php echo count($usuarios); ?></strong></p>
          <?php if ($columnas): ?>
            <p class="mb-0 small text-muted"><strong>Columnas de usuarios:</strong> <?php echo htmlspecialchars(implode(', ', $columnas)); ?></p>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <div class="card admin-card">
        <div class="card-header">Usuarios registrados</div>
        <div class="card-body">
          <?php if (!$usuarios): ?>
            <div class="p-4">No hay usuarios en la tabla <code>usuarios</code>, o no se pudo leer la tabla.</div>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover pcr-table w-100">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Email / usuario</th>
                  <th>Tipo</th>
                  <th>Stat</th>
                  <th>Hash password</th>
                  <th>Nueva contraseña</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($usuarios as $u): ?>
                <?php
                  $id = (int) col_val($u, ['id'], 0);
                  $nombre = col_val($u, ['nombre', 'name', 'nombre_completo']);
                  $email = col_val($u, ['email', 'correo', 'usuario']);
                  $tipo = col_val($u, ['tipo_user', 'tipo_usuario', 'rol']);
                  $stat = col_val($u, ['stat', 'estado', 'status']);
                  $hash = (string) col_val($u, ['password', 'pass', 'clave']);
                  $hashCorto = $hash !== '' ? substr($hash, 0, 18) . '…' : '(vacío)';
                ?>
                <tr>
                  <td><strong>#<?php echo $id; ?></strong></td>
                  <td><?php echo htmlspecialchars((string) $nombre); ?></td>
                  <td><?php echo htmlspecialchars((string) $email); ?></td>
                  <td><?php echo htmlspecialchars((string) $tipo); ?></td>
                  <td><?php echo htmlspecialchars((string) $stat); ?></td>
                  <td class="small text-muted"><?php echo htmlspecialchars($hashCorto); ?></td>
                  <td>
                    <form method="post" class="d-flex gap-1">
                      <input type="hidden" name="accion" value="cambiar_password">
                      <input type="hidden" name="id" value="<?php echo $id; ?>">
                      <input type="text" name="nueva_password" class="form-control form-control-sm" placeholder="Nueva clave" required>
                      <button class="btn btn-sm btn-pcr-primary" type="submit">Guardar</button>
                    </form>
                  </td>
                  <td>
                    <form method="post">
                      <input type="hidden" name="accion" value="entrar">
                      <input type="hidden" name="id" value="<?php echo $id; ?>">
                      <button class="btn btn-sm btn-success" type="submit">Entrar</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <p class="mt-3"><a href="index.php">Volver al login normal</a></p>
    </main>
  </body>
</html>
