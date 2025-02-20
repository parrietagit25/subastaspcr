<?php 
session_start();
$mensaje = "";

try {
  $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}

/*
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
use Twilio\Rest\Client; */

if (isset($_POST['msn_send']) && $_POST['msn_send'] == 'ind') {

    //$asunto = $_POST['asunto'];
    $contenido = $_POST['contenido'];
    $numero_telefono = $_POST['telefono'];

    // aqui van los apis

    $sid = '';
    $token = '';
	
    $twilio = new Client($sid, $token);

    $message = $twilio->messages
      ->create("+507".$numero_telefono, // to
        array(
              "body" => "".$contenido."",
	      "from" => "+13148606586",
        )
      );

print($message->sid);

}elseif(isset($_POST['msn_send']) && $_POST['msn_send'] == 'todos'){

    $contenido = $_POST['contenido'];


    $todos_contact = $pdo -> query("SELECT * FROM cc_subastas WHERE stat =2");
    $rows = $todos_contact->fetchAll(PDO::FETCH_ASSOC); 
    foreach ($rows as $key => $value) {

      $twilio = new Client($sid, $token);

      $message = $twilio->messages
        ->create("+507".$value['telefono'], // to
          array(
                "body" => "".$contenido."", 
		"from" => "+13148606586",
          )
        );
    
    }
}

if(!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="https://getbootstrap.com/docs/5.3/assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.115.4">
    <title>Mantenimiento de subastas</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://getbootstrap.com/docs/5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="theme-color" content="#712cf9">
    <link href="https://getbootstrap.com/docs/5.3/examples/sign-in/sign-in.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.css" rel="stylesheet">
  </head>
  <body class="align-items-center bg-body-tertiary">
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
      <symbol id="check2" viewBox="0 0 16 16">
        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
      </symbol>
      <symbol id="circle-half" viewBox="0 0 16 16">
        <path d="M8 15A7 7 0 1 0 8 1v14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
      </symbol>
      <symbol id="moon-stars-fill" viewBox="0 0 16 16">
        <path d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"/>
        <path d="M10.794 3.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387a1.734 1.734 0 0 0-1.097 1.097l-.387 1.162a.217.217 0 0 1-.412 0l-.387-1.162A1.734 1.734 0 0 0 9.31 6.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387a1.734 1.734 0 0 0 1.097-1.097l.387-1.162zM13.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732l-.774-.258a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L13.863.1z"/>
      </symbol>
      <symbol id="sun-fill" viewBox="0 0 16 16">
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z"/>
      </symbol>
    </svg>

    <div class="dropdown position-fixed bottom-0 end-0 mb-3 me-3 bd-mode-toggle">
      <button class="btn btn-bd-primary py-2 dropdown-toggle d-flex align-items-center"
              id="bd-theme"
              type="button"
              aria-expanded="false"
              data-bs-toggle="dropdown"
              aria-label="Toggle theme (auto)">
        <svg class="bi my-1 theme-icon-active" width="1em" height="1em"><use href="#circle-half"></use></svg>
        <span class="visually-hidden" id="bd-theme-text">Toggle theme</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bd-theme-text">
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
            <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#sun-fill"></use></svg>
            Light
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="dark" aria-pressed="false">
            <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#moon-stars-fill"></use></svg>
            Dark
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="auto" aria-pressed="true">
            <svg class="bi me-2 opacity-50 theme-icon" width="1em" height="1em"><use href="#circle-half"></use></svg>
            Auto
            <svg class="bi ms-auto d-none" width="1em" height="1em"><use href="#check2"></use></svg>
          </button>
        </li>
      </ul>
    </div>

    <?php include('menu.php'); ?>

    <main class="container">
        <div class="container">
        
            <h2>Enviar Mensaje de texto</h2>
            <?php echo $mensaje; ?>
            <form method="post" action="">

                <div class="form-group">
                  <label for="">Todos</label>
                  <input type="radio" name="msn_send" value="todos" id="todos" onclick="telefono_inv()">
                  <label for="">Individual</label>
                  <input type="radio" name="msn_send" value="ind" id="ind" onclick="mostrar_telefono_inv()">
                </div>
                <div class="form-group" id="telefono_indiv" style="display:none;">
                    <label for="asunto">Ingrese el Telefono:</label>
                    <select name="telefono" id="" class="form-control">
                      <option value="">Seleccionar</option>
                      <?php $ultimo_id = $pdo -> query("SELECT * FROM cc_subastas WHERE stat =2");
                            $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC); 
                             foreach ($rows as $key => $value) { ?>
                              <option value="<?php echo $value['telefono']; ?>"><?php echo $value['nombre_completo'] . ' || ' . $value['telefono']; ?></option>
                            <?php } ?>
                    </select>
                </div>
                <!--<div class="form-group">
                    <label for="asunto">Asunto:</label>
                    <input type="text" class="form-control" id="asunto" name="asunto" required>
                </div>-->
                <div class="form-group">
                    <label for="contenido">Contenido:</label>
                    <textarea name="contenido" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>

        </div>
    </main>
    <script src="https://getbootstrap.com/docs/5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.js"></script>
    <script>
        $(document).ready(function() {
            $('#contenido').summernote({
                height: 200
            });
        });
    </script>
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
