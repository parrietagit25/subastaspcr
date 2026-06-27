<?php 
session_start();
$mensaje = "";
require 'vendor/autoload.php';
require_once 'config/mail.php';
require_once 'config/ui_helpers.php';

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

if (isset($_POST['send_email'])) {

  $result = send_email(
      $_POST['email_send_email'],
      'GRUPO PCR - Revicion de Documentos',
      '
      <html>
        <head>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        </head>
        <body> 
          <p>Buen dia, el siguiente mensaje fue enviado por parte del personal administrativo, el mensaje enviado es:</p>
          " - '.$_POST['mensaje_send_email'].' - "
        </body>
      </html>
      ',
      'Subastas Grupo PCR',
      mail_default_logos()
  );

  if (!$result['ok']) {
      echo "El mensaje no se pudo enviar. Error: {$result['error']}";
  }
  
  $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Mensaje Enviado!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
  
}

  if (isset($_POST['id_aprobar'])) {

    $codigo = $_POST['id_aprobar'].rand(1, 100000);

    $insert = $pdo -> query("UPDATE cc_subastas SET codigo = '".$codigo."', stat = 2 WHERE id = '".$_POST['id_aprobar']."'");
    
    $datos_user = $pdo -> query("SELECT * FROM cc_subastas WHERE id = '".$_POST['id_aprobar']."'");
    $rowss = $datos_user->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rowss as $rows) {

      $nombre=$rows['nombre_completo'];
      $email_destinatario = $rows['email'];

     }

    $result = send_email(
        $email_destinatario,
        'GRUPO PCR - APROBADO',
        '
        <html>
          <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
          </head>
          <body> 
            <img src="cid:logosubastas" width="250" alt="Logo 1" />
            <br>
            <p>Estimado, '.$nombre.'</p>
            <p>Esperamos que este mensaje le encuentre bien. Nos complace informarle que su proceso de registro con el Grupo PCR ha sido aprobado exitosamente.</p>
            <p><strong style="font-size: 18px;">Código de Continuación de Registro: '.$codigo.'</strong></p>
            <p>Por favor, utilice el código proporcionado para continuar con las siguientes etapas de su registro en nuestra plataforma web <a href="https://subastas.grupopcr.com.pa/">https://subastas.grupopcr.com.pa/</a> entra en nuestra pagina e ingresa el codigo.</p>
            <p>Si tiene alguna pregunta o necesita más información, no dude en ponerse en contacto con nosotros. Estamos aquí para ayudarle en todo </p>
            <p>lo que necesite para asegurar una transición fluida.</p>
            <p>¡Muchas gracias por elegir el Grupo PCR! Esperamos tener una relación larga y fructífera con usted.</p>
            <br>
            Atentamente,
            <br>
            El Equipo del Grupo PCR
            <br>
            <img src="cid:logogrupopcr" width="250" alt="Logo 2" />
          </body>
        </html>
        ',
        'Subastas Grupo PCR',
        mail_default_logos()
    );

    if (!$result['ok']) {
        echo "El mensaje no se pudo enviar. Error: {$result['error']}";
    }
    
    $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Persona Aprobada! Codigo enviado por correo</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    
  }

  if (isset($_POST['id_eliminar'])) {

    $insert = $pdo -> query("UPDATE cc_subastas SET stat = 3 WHERE id = '".$_POST['id_eliminar']."'");

    $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Registro Eliminado!</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    
  }

  if (isset($_POST['id_aprobar_supervisor'])) {
    # code...
    $insert = $pdo -> query("UPDATE cc_subastas SET stat = 4 WHERE id = '".$_POST['id_aprobar_supervisor']."'");

    $result = send_email(
        [
            'yamileth.rodriguez@grupopcr.com.pa',
            'ilany.albeo@grupopcr.com.pa',
        ],
        'GRUPO PCR - Revicion de Documentos',
        '
        <html>
          <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
          </head>
          <body> 
            <p>Buen dia, se acaba de aprobar unos documentos para su revicion: <br>
              puede ingresar a <a href="https://subastas.grupopcr.com.pa/subasta/main.php" target="_blank">https://subastas.grupopcr.com.pa/subasta/</a> para ver los detalles.</p>
          </body>
        </html>
        ',
        'Subastas Grupo PCR',
        mail_default_logos()
    );

    if (!$result['ok']) {
        echo "El mensaje no se pudo enviar. Error: {$result['error']}";
    }

    $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Registro enviado al supervisor!</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
  }

  if($_SESSION["tipo_user"] == 'vendedor'){ 
    $ultimo_id = $pdo -> query("SELECT * FROM cc_subastas WHERE stat =1");
    $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);
  }elseif ($_SESSION["tipo_user"] == 'supervisor') {
    $ultimo_id = $pdo -> query("SELECT * FROM cc_subastas WHERE stat =4");
    $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);
  }elseif ($_SESSION["tipo_user"] == 'admin') {
    $ultimo_id = $pdo -> query("SELECT * FROM cc_subastas");
    $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);
  }

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitudes - Subastas PCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include('includes/admin_assets.php'); ?>
  </head>
  <body class="admin-body">
    <?php include('menu.php'); ?>
    <!-- Modal Aprobar -->
    <div class="modal fade" id="aprobacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Aprobar</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body" id="aprobar">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelar">Cerrar</button>
              <button type="submit" class="btn btn-primary" name="aprobar_final" id="enviar">Aprobar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="aprobacion_supervisor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Aprobar y enviar al supervisor</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body" id="aprobar_supervidor">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelar">Cerrar</button>
              <button type="submit" class="btn btn-primary" name="aprobar_supervisor" id="">Enviar al supervisor</button>
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
        <h1>Solicitudes</h1>
        <p>Gestión de registros pendientes y en revisión</p>
    </div>
    <div class="card admin-card">
        <div class="card-body">
        <table id="example" class="table table-hover pcr-table w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><strong>#<?php echo $row['id']; ?></strong></td>
                    <td><?php echo tipo_persona_badge($row['tipo_persona']); ?></td>
                    <td class="text-start"><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['date_time'])); ?></td>
                    <td><?php echo estado_solicitud_badge((int) $row['stat']); ?></td>
                    <td>
                    <div class="btn-actions">
                    <?php if($_SESSION["tipo_user"] == 'vendedor'){ ?>
                    <a type="button" class="btn btn-action btn-email" data-bs-toggle="modal" data-bs-target="#send_email" onclick="enviar_email(<?php echo $row['id']; ?>)" title="Enviar correo">
                      <i class="bi bi-envelope"></i>
                    </a>
                    <?php } ?>

                    <?php if($_SESSION["tipo_user"] == 'vendedor'){ ?>
                    <a type="button" class="btn btn-action btn-approve" data-bs-toggle="modal" data-bs-target="#aprobacion_supervisor" onclick="aprobar_supervisor(<?php echo $row['id']; ?>)" title="Enviar a supervisor">
                      <i class="bi bi-send-check"></i>
                    </a>
                    <?php } ?>

                    <?php if($_SESSION["tipo_user"] == 'supervisor'){ ?>
                    <a type="button" class="btn btn-action btn-approve" data-bs-toggle="modal" data-bs-target="#aprobacion" onclick="aprobar(<?php echo $row['id']; ?>)" title="Aprobar solicitud">
                      <i class="bi bi-check-lg"></i>
                    </a>
                    <?php } ?>
                      
                      <a type="button" class="btn btn-action btn-docs" data-bs-toggle="modal" data-bs-target="#adjuntos" onclick="adjuntos(<?php echo $row['id']; ?>)" title="Ver documentos">
                        <i class="bi bi-file-earmark-pdf"></i>
                      </a>

                      <?php if($_SESSION["tipo_user"] == 'admin'){ ?>
                      <a type="button" class="btn btn-action btn-delete" data-bs-toggle="modal" data-bs-target="#eliminar" onclick="eliminar(<?php echo $row['id']; ?>)" title="Eliminar">
                        <i class="bi bi-trash3"></i>
                      </a>
                      <?php } ?>
                        <a class="btn btn-action btn-upload" href="adjuntos.php?id_subir=<?php echo $row['id']; ?>" title="Subir adjunto">
                          <i class="bi bi-upload"></i>
                        </a>
                    </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include('includes/datatables_init.php'); ?>
<script>
    function aprobar(x){

      fetch('consultas.php?aprobar=1&id=' + x)
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

    function aprobar_supervisor(x){
      
      fetch('consultas.php?aprobar_supervisor=1&id=' + x)
        .then(response => {
          
          if (!response.ok) {
            throw new Error('Error de red al intentar fetch.');
          }
          return response.text();
        })
        .then(data => {
          
          document.querySelector("#aprobar_supervidor").innerHTML = data;
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

      fetch('consultas.php?eliminar=1&id=' + x)
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

