<?php // vole 
session_start();
$mensaje = "";
require 'vendor/autoload.php';
require_once 'config/ui_helpers.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

try {
  $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}

if (isset($_POST['editar_usuario'])) {

    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $edad = $_POST['edad'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $tipo_user = $_POST['tipo_user'];
    $stat = $_POST['stat'];

    try {
        // Construir la consulta dinámica según si hay password o no
        if ($password) {
            $sql = "UPDATE usuarios 
                    SET nombre = '$nombre', email = '$email', edad = '$edad', password = '$password', tipo_user = '$tipo_user', stat = '$stat' 
                    WHERE id = '$id'";
        } else {
            $sql = "UPDATE usuarios 
                    SET nombre = '$nombre', email = '$email', edad = '$edad', tipo_user = '$tipo_user', stat = '$stat' 
                    WHERE id = '$id'";
        }

        // Ejecutar la consulta
        $insert = $pdo->query($sql);

        if ($insert) {
            $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Usuario actualizado</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Error al actualizar el usuario.</div>';
        }

    } catch (PDOException $e) {
        echo '<div class="alert alert-danger" role="alert">Error: ' . $e->getMessage() . '</div>';
    }
}


  if (isset($_POST['id_eliminar'])) {

    $insert = $pdo -> query("UPDATE usuarios SET stat = 3 WHERE id = '".$_POST['id_eliminar']."'");

    $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Registro Eliminado!</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    
  }

  $ultimo_id = $pdo -> query("SELECT * FROM usuarios");
  $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);

?>
<?php $pageTitle = 'Usuarios - Subastas PCR'; ?>
<?php include('includes/admin_layout_open.php'); ?>
    <!-- Modal Aprobar -->
    <div class="modal fade" id="editar_users" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Usuario</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body" id="aprobar">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelar">Cerrar</button>
              <button type="submit" class="btn btn-primary" name="editar_usuario" id="">Guardar cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="send_email" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Enviar Email</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body" id="enviar_email_cont">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelar">Cerrar</button>
              <button type="submit" class="btn btn-primary" name="send_email" id="enviar">Enviar Email</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal adjuntos -->
    <div class="modal fade" id="adjuntos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Adjuntos</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body" id="adjuntos_conte">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Eliminar -->
    <div class="modal fade" id="eliminar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Adjuntos</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body" id="eliminar_conte">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-danger" name="aprobar_final">Eliminar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    
<main class="admin-main">
        <?php echo $mensaje; ?>
        <div class="page-header">
            <h1>Usuarios del sistema</h1>
            <p>Administración de cuentas internas</p>
        </div>
        <div class="card admin-card">
        <div class="card-body">
        <table id="example" class="table table-hover pcr-table w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                    <td class="text-start"><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo tipo_usuario_badge($row['tipo_user']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo estado_usuario_badge((int) $row['stat']); ?></td>
                    <td>
                    <div class="btn-actions">
                      <?php if($_SESSION["tipo_user"] == 'admin'){ ?>
                        <a type="button" class="btn btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editar_users" onclick="aprobar(<?php echo $row['id']; ?>)" title="Editar usuario">
                            <i class="bi bi-pencil"></i>
                        </a>
                      <?php } ?>

                      <?php if($_SESSION["tipo_user"] == 'admin'){ ?>
                      <a type="button" class="btn btn-action btn-delete" data-bs-toggle="modal" data-bs-target="#eliminar" onclick="eliminar(<?php echo $row['id']; ?>)" title="Eliminar usuario">
                        <i class="bi bi-trash3"></i>
                      </a>
                      <?php } ?>
                    </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
        </div>
</main>
<?php include('includes/admin_layout_close.php'); ?>
<script>
    function aprobar(x){

      fetch('consultas.php?editar_usuario=1&id=' + x)
        .then(response => {
          
          if (!response.ok) {
            throw new Error('Error de red al intentar fetch.');
          }
          return response.text();
        })
        .then(data => {
          
          document.querySelector("#aprobar").innerHTML = data;
        })
        .catch(error => {
          
          console.error("Hubo un problema con la operación fetch:", error);
        });

    }

    function enviar_email(x){

      fetch('consultas.php?enviar_email=1&id=' + x)
        .then(response => {
          
          if (!response.ok) {
            throw new Error('Error de red al intentar fetch.');
          }
          return response.text();
        })
        .then(data => {
          
          document.querySelector("#enviar_email_cont").innerHTML = data;
        })
        .catch(error => {
          
          console.error("Hubo un problema con la operación fetch:", error);
        });

    }

    function adjuntos(x){

      fetch('consultas.php?adjuntos=1&id=' + x)
        .then(response => {
          
          if (!response.ok) {
            throw new Error('Error de red al intentar fetch.');
          }
          return response.text();
        })
        .then(data => {
          
          document.querySelector("#adjuntos_conte").innerHTML = data;
        })
        .catch(error => {
          
          console.error("Hubo un problema con la operación fetch:", error);
        });

    }

    function eliminar(x){

      fetch('consultas.php?eliminar_usuario=1&id=' + x)
        .then(response => {
          
          if (!response.ok) {
            throw new Error('Error de red al intentar fetch.');
          }
          return response.text();
        })
        .then(data => {
          
          document.querySelector("#eliminar_conte").innerHTML = data;
        })
        .catch(error => {
          
          console.error("Hubo un problema con la operación fetch:", error);
        });

    }


    document.addEventListener('DOMContentLoaded', function() {

      const form = document.querySelector('form');
      const enviar = document.getElementById('enviar');
      const cancelar = document.getElementById('cancelar');

      form.addEventListener('submit', function(event) {

        enviar.disabled = true;
        enviar.value = 'Aprobando...'; 
        cancelar.disabled = true;
        cancelar.value = 'Aprobando...'; 

      });
    });

</script>
    </body>
</html>

