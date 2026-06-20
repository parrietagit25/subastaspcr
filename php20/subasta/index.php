<?php 
session_start();
$mensaje = "";

if(isset($_SESSION["email"])) {
    header("Location: main.php");
    exit();
}

if (isset($_POST['email'])) {

    try {
        $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $comprobar_user = $pdo -> query("SELECT id, password, tipo_user FROM usuarios WHERE email  = '".$email."'");
    $rows = $comprobar_user->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
      $id = $row['id'];
      $pass = $row['password'];
      $tipo_user = $row['tipo_user'];
    }

    if (isset($pass)) {

        if (password_verify($password, $pass)) {
            session_start();
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["email"] = $email;
            $_SESSION["tipo_user"] = $tipo_user;
            header("Location: main.php");
        } else {
            $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Contraseña incorrecta</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        }
        
    }else{

    $mensaje = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Usuario incorrecto</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';

    }
        
}

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subastas PCR - Acceso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin-corporate.css" rel="stylesheet">
  </head>
  <body>
    <div class="login-page">
      <div class="login-card">
        <?php echo $mensaje; ?>
        <form action="" method="POST">
          <img src="logogrupopcr.png" alt="Grupo PCR">
          <h1>Panel de administración</h1>

          <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" required>
            <label for="floatingInput">Correo electrónico</label>
          </div>
          <div class="form-floating mb-4">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
            <label for="floatingPassword">Contraseña</label>
          </div>

          <button class="btn btn-pcr-primary w-100" type="submit">Ingresar</button>
          <p class="login-footer">&copy; <?php echo date('Y'); ?> Grupo PCR</p>
        </form>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
