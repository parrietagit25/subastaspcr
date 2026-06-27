<?php 
session_start();
$mensaje = "";

try {
  $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}


require 'vendor/autoload.php';
require_once 'config/mail.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $asunto = $_POST['asunto'];
    $contenido = $_POST['contenido'];

    if (isset($_POST['email_send']) && $_POST['email_send'] == 'todos') {

      $ultimo_id = $pdo -> query("SELECT email FROM cc_subastas group by email");
      $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);

      foreach ($rows as $key => $value) {
        $result = send_email($value['email'], $asunto, $contenido, 'Notificaciones PCR');

        if ($result['ok']) {
            $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                          <strong>El correo ha sido enviado a '.$value['email'].'</strong>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error al enviar el correo a '.$value['email'].': '.$result['error'].'</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
        }
      }
  
    }elseif (isset($_POST['email_send']) && $_POST['email_send'] == 'ind') {
      
      $result = send_email($_POST['email'], $asunto, $contenido, 'Notificaciones PCR');

      if ($result['ok']) {
        $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>El correo ha sido enviado</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
      } else {
          echo "Error al enviar el correo: {$result['error']}";
      }

    }

}

if(!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notificaciones - Subastas PCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include('includes/admin_assets.php'); ?>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
  </head>
  <body class="admin-body">
    <?php include('menu.php'); ?>

    <main class="admin-main">
      <div class="page-header">
        <h1><i class="bi bi-envelope-paper me-2"></i>Enviar correo</h1>
        <p>Notifique a todos los registrados o a un destinatario individual</p>
      </div>

      <div class="form-card">
        <?php echo $mensaje; ?>
        <form method="post" action="">
          <div class="radio-group">
            <label>
              <input type="radio" name="email_send" value="todos" id="todos" onclick="email_inv()" required>
              Todos los registrados
            </label>
            <label>
              <input type="radio" name="email_send" value="ind" id="ind" onclick="mostrar_email_inv()">
              Individual
            </label>
          </div>

          <div class="mb-3" id="email_indiv" style="display:none;">
            <label for="email" class="form-label">Correo del destinatario</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com">
          </div>

          <div class="mb-3">
            <label for="asunto" class="form-label">Asunto</label>
            <input type="text" class="form-control" id="asunto" name="asunto" required>
          </div>

          <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea id="contenido" name="contenido"></textarea>
          </div>

          <button type="submit" class="btn btn-pcr-primary">
            <i class="bi bi-send me-1"></i> Enviar correo
          </button>
        </form>
      </div>
    </main>

    <?php include('includes/admin_layout_close.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#contenido').summernote({
                height: 220,
                placeholder: 'Escriba el contenido del correo...'
            });
        });

        function mostrar_email_inv(){
          document.querySelector("#email_indiv").style.display = "block";
        }

        function email_inv(){
          document.querySelector("#email_indiv").style.display = "none";
        }
    </script>
  </body>
</html>
