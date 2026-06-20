<?php 
session_start();
require 'vendor/autoload.php';
require_once 'config/mail.php';
require_once 'config/ui_helpers.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

$mensaje="";

try {
    $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
  }

  if (isset($_POST['reenviar_codigo'])) {

    $datos_user = $pdo -> query("SELECT * FROM cc_subastas WHERE id = '".$_POST['id_reenviar']."'");
    $rowss = $datos_user->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rowss as $rows) {

      $nombre=$rows['nombre_completo'];
      $email_destinatario = $rows['email'];
      $codigo = $rows['codigo'];

     }

    $mail = new PHPMailer(true);

    try {
        configure_mailer($mail, 'Subastas Grupo PCR');

        // Destinatarios
        $mail->addAddress($email_destinatario, $nombre);

        // Contenido del correo
        $mail->CharSet = 'UTF-8';
        $mail->IsHTML(true);

        $mail->Subject = 'GRUPO PCR - APROBADO';
        $mail->Body    = '
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
        ';

        $mail->AddEmbeddedImage('../img/logo20años.png', 'logogrupopcr');
        $mail->AddEmbeddedImage('../img/logosubastas.png', 'logosubastas');

        $mail->send();
        //echo 'El mensaje ha sido enviado';
    } catch (Exception $e) {
        echo "El mensaje no se pudo enviar. Error: {$mail->ErrorInfo}";
    } 
    
    $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Persona Aprobada! Codigo enviado por correo</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    
  }


if (isset($_POST['edit_user'])) {

  $update_user = $pdo -> query("UPDATE cc_subastas SET tipo_persona = '".$_POST['edit_tipo_persona']."', 
                                                        nombre_completo = '".$_POST['edit_nombre_completo']."', 
                                                        email = '".$_POST['edit_email']."', 
                                                        telefono = '".$_POST['edit_telefono']."' 
                                                        WHERE 
                                                        id = '".$_POST['id_user_edit']."' ");

  $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Persona Actualizada!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
  
}

  $ultimo_id = $pdo -> query("SELECT * FROM cc_subastas WHERE stat =2");
  $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);


?>
<?php $pageTitle = 'Aprobados - Subastas PCR'; ?>
<?php include('includes/admin_layout_open.php'); ?>

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
              <button type="submit" class="btn btn-primary" name="aprobar_final">Aprobar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal reenviar codigo -->
    <div class="modal fade" id="reenviar_codigo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Reenviar Codigo</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body" id="codigo_conte">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary" name="reenviar_codigo">Aprobar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal edtar usuario -->
    <div class="modal fade" id="edit_user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Usuario</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body" id="edit_conten">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary" name="edit_user">Editar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

<main class="admin-main">
     <?php echo $mensaje; ?>
     <div class="page-header">
        <h1>Registros aprobados</h1>
        <p>Participantes con acceso autorizado a la plataforma</p>
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
                      
                      <a type="button" class="btn btn-action btn-docs" data-bs-toggle="modal" data-bs-target="#adjuntos" onclick="adjuntos(<?php echo $row['id']; ?>)" title="Ver documentos">
                        <i class="bi bi-file-earmark-pdf"></i>
                      </a>

                      <a type="button" class="btn btn-action btn-resend" data-bs-toggle="modal" data-bs-target="#reenviar_codigo" onclick="reenviar_codigo(<?php echo $row['id']; ?>)" title="Reenviar código">
                        <i class="bi bi-send"></i>
                      </a>

                      <a type="button" class="btn btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#edit_user" onclick="editar_reg(<?php echo $row['id']; ?>)" title="Editar registro">
                          <i class="bi bi-pencil"></i>
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
<?php include('includes/admin_layout_close.php'); ?>
<script>
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

    function reenviar_codigo(x){

      fetch('consultas.php?reenviar_codigo=1&id=' + x)
        .then(response => {
          
          if (!response.ok) {
            throw new Error('Error de red al intentar fetch.');
          }
          return response.text();
        })
        .then(data => {
          
          document.querySelector("#codigo_conte").innerHTML = data;
        })
        .catch(error => {
          
          console.error("Hubo un problema con la operación fetch:", error);
        });

      }

      function editar_reg(x){

        fetch('consultas.php?edit_reg=1&id=' + x)
          .then(response => {
            
            if (!response.ok) {
              throw new Error('Error de red al intentar fetch.');
            }
            return response.text();
          })
          .then(data => {
            
            document.querySelector("#edit_conten").innerHTML = data;
          })
          .catch(error => {
            
            console.error("Hubo un problema con la operación fetch:", error);
          });

      }

</script>
    </body>
</html>
