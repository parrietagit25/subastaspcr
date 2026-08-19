<?php
session_start();
$mensaje = "";

if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

try {
    $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

require 'vendor/autoload.php';
use Twilio\Rest\Client;

$sid = '';
$token = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['msn_send'])) {
    $contenido = trim((string) ($_POST['contenido'] ?? ''));

    if ($contenido === '') {
        $mensaje = '<div class="alert alert-warning">Escriba el contenido del mensaje.</div>';
    } elseif ($sid === '' || $token === '') {
        $mensaje = '<div class="alert alert-danger">Twilio no está configurado (SID/token vacíos).</div>';
    } elseif ($_POST['msn_send'] === 'ind') {
        $numero_telefono = $_POST['telefono'] ?? '';
        try {
            $twilio = new Client($sid, $token);
            $twilio->messages->create('+507' . $numero_telefono, [
                'body' => $contenido,
                'from' => '+13148606586',
            ]);
            $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                          <strong>SMS enviado a ' . htmlspecialchars($numero_telefono) . '</strong>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } catch (Exception $e) {
            $mensaje = '<div class="alert alert-danger">Error al enviar SMS: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } elseif ($_POST['msn_send'] === 'todos') {
        $todos_contact = $pdo->query("SELECT * FROM cc_subastas WHERE stat = 2");
        $rows = $todos_contact->fetchAll(PDO::FETCH_ASSOC);
        $enviados = 0;
        try {
            $twilio = new Client($sid, $token);
            foreach ($rows as $value) {
                $twilio->messages->create('+507' . $value['telefono'], [
                    'body' => $contenido,
                    'from' => '+13148606586',
                ]);
                $enviados++;
            }
            $mensaje = '<div class="alert alert-success">SMS enviado a ' . $enviados . ' contactos aprobados.</div>';
        } catch (Exception $e) {
            $mensaje = '<div class="alert alert-danger">Error al enviar SMS: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

$contactos = [];
try {
    $contactos = $pdo->query("SELECT nombre_completo, telefono FROM cc_subastas WHERE stat = 2 ORDER BY nombre_completo")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $contactos = [];
}

$pageTitle = 'SMS - Subastas PCR';
include('includes/admin_layout_open.php');
?>

    <main class="admin-main">
      <div class="page-header">
        <h1><i class="bi bi-chat-dots me-2"></i>Enviar mensaje de texto</h1>
        <p>Notifique por SMS a registrados aprobados, de forma masiva o individual</p>
      </div>

      <div class="form-card">
        <?php echo $mensaje; ?>
        <form method="post" action="">
          <div class="radio-group">
            <label>
              <input type="radio" name="msn_send" value="todos" id="todos" onclick="telefono_inv()" required>
              Todos los aprobados
            </label>
            <label>
              <input type="radio" name="msn_send" value="ind" id="ind" onclick="mostrar_telefono_inv()">
              Individual
            </label>
          </div>

          <div class="mb-3" id="telefono_indiv" style="display:none;">
            <label for="telefono" class="form-label">Teléfono del destinatario</label>
            <select name="telefono" id="telefono" class="form-select">
              <option value="">Seleccionar</option>
              <?php foreach ($contactos as $value) { ?>
                <option value="<?php echo htmlspecialchars($value['telefono']); ?>">
                  <?php echo htmlspecialchars($value['nombre_completo'] . ' — ' . $value['telefono']); ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="contenido" class="form-label">Mensaje</label>
            <textarea name="contenido" id="contenido" class="form-control" rows="6" maxlength="1600" placeholder="Escriba el SMS..." required></textarea>
            <div class="form-text">Texto plano. Recomendado: hasta 160 caracteres por SMS.</div>
          </div>

          <button type="submit" class="btn btn-pcr-primary">
            <i class="bi bi-send me-1"></i> Enviar SMS
          </button>
        </form>
      </div>
    </main>

    <?php include('includes/admin_layout_close.php'); ?>
    <script>
      function mostrar_telefono_inv(){
        document.querySelector("#telefono_indiv").style.display = "block";
      }
      function telefono_inv(){
        document.querySelector("#telefono_indiv").style.display = "none";
      }
    </script>
  </body>
</html>
