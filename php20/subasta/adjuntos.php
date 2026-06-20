<?php 
session_start();
$mensaje = '';
if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

try {
    $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$id_subir_adjunto = $_GET['id_subir'] ?? 0;

$stmt = $pdo->prepare("SELECT nombre_completo FROM cc_subastas WHERE id = ?");
$stmt->execute([$id_subir_adjunto]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre = $row ? $row['nombre_completo'] : 'Desconocido';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_subir_adjunto'])) {
    $id_user = $_POST['id_subir_adjunto'];
    $descripcion = $_POST['descripcion'] ?? '';
    $adjunto = $_FILES['adjunto']['name'] ?? null;
    $adjunto_temp = $_FILES['adjunto']['tmp_name'] ?? null;

    $upload_dir = 'adjuntos_users_cc/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if ($adjunto_temp) {
        $adjunto_destino = $upload_dir . basename($adjunto);
        if (move_uploaded_file($adjunto_temp, $adjunto_destino)) {
            $insert_stmt = $pdo->prepare("INSERT INTO cc_adjuntos_user (id_user, nombre, adjunto, descripcion) VALUES (?, ?, ?, ?)");
            if ($insert_stmt->execute([$id_user, $nombre, $adjunto_destino, $descripcion])) {
                echo '<div class="alert alert-success" role="alert">Adjunto subido correctamente.</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error al guardar los datos en la base de datos.</div>';
            }
        } else {
            echo '<div class="alert alert-danger" role="alert">Error al mover el archivo.</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">No se seleccionó ningún archivo.</div>';
    }
}
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Adjuntos - Subastas PCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include('includes/admin_assets.php'); ?>
  </head>
  <body class="admin-body">
    <?php include('menu.php'); ?>

    <div class="modal fade" id="subir_adjuntos" tabindex="-1" aria-labelledby="subirAdjuntosLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="subirAdjuntosLabel">Subir adjunto</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="mb-3">
                    <label for="adjunto" class="form-label">Archivo</label>
                    <input type="file" class="form-control" id="adjunto" name="adjunto" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-pcr-primary" name="send_email">
                <i class="bi bi-upload me-1"></i> Subir adjunto
              </button>
              <input type="hidden" name="id_subir_adjunto" value="<?php echo htmlspecialchars((string) $id_subir_adjunto); ?>">
            </div>
          </form>
        </div>
      </div>
    </div>

    <main class="admin-main">
      <div class="page-header d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div>
          <h1><i class="bi bi-paperclip me-2"></i>Adjuntos</h1>
          <p>Documentos de <?php echo htmlspecialchars($nombre); ?></p>
        </div>
        <button class="btn btn-pcr-primary" data-bs-toggle="modal" data-bs-target="#subir_adjuntos">
          <i class="bi bi-plus-lg me-1"></i> Subir adjunto
        </button>
      </div>

      <div class="card admin-card">
        <div class="card-body">
        <table id="example" class="table table-hover pcr-table w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Adjunto</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $adjuntos = $pdo -> query("SELECT * FROM cc_adjuntos_user WHERE id_user = '".$id_subir_adjunto."'");
                      foreach ($adjuntos as $row2) { ?>
                <tr>
                    <td><strong>#<?php echo $row2['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($row2['nombre']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row2['fecha_log'])); ?></td>
                    <td><a href="<?php echo htmlspecialchars($row2['adjunto']); ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark-arrow-down me-1"></i>Ver</a></td>
                    <td><?php echo htmlspecialchars($row2['descripcion']); ?></td>
                    <td>
                      <?php if($_SESSION["tipo_user"] == 'admin' || $_SESSION["tipo_user"] == 'supervisor'){ ?>
                      <a type="button" class="btn btn-action btn-delete" data-bs-toggle="modal" data-bs-target="#eliminar" onclick="eliminar(<?php echo $row2['id']; ?>)" title="Eliminar">
                        <i class="bi bi-trash3"></i>
                      </a>
                      <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
      </div>
    </main>

    <?php include('includes/admin_layout_close.php'); ?>
  </body>
</html>

