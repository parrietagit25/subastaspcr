<?php
$mensaje = "";

try {
  $pdo = new PDO('mysql:host=db;dbname=db;charset=utf8mb4', 'parrieta', 'Dollar2022');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}

if (isset($_POST['nombre_pn'])) {

    $insert = $pdo -> query("INSERT INTO cc_subastas(nombre_completo, email, telefono, tipo_persona, stat)VALUES('".$_POST['nombre_pn']."', '".$_POST['email_pn']."', '".$_POST['telefono_pn']."', 'NATURAL', 1)");
    
    $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Registro Realizado!</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    
    $ultimo_id = $pdo -> query("SELECT MAX(id) as id FROM cc_subastas");
    $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
      $id_ult = $row['id'];
    }

    // recibo

    if (!empty($_FILES["recibo_pn"]["name"])) {

      $target_dir = "recibo_pn/";  

      $uniqueFileName = uniqid() . "-" . time();
      $fileExtension = pathinfo($_FILES["recibo_pn"]["name"], PATHINFO_EXTENSION);
      $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
      $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
      $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
      $check = getimagesize($_FILES["recibo_pn"]["tmp_name"]);

      if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

        if (move_uploaded_file($_FILES["recibo_pn"]["tmp_name"], $target_file)) {

          $update = $pdo -> query("UPDATE cc_subastas SET pn_recibo_servicios = '".$target_file."' WHERE id = '".$id_ult."'");
          
        } else {
          echo "Lo siento, ha ocurrido un error al subir tu archivo.";
        }
    
      } else {
        echo "El archivo no es una imagen.";
      }

    }

    // ficha

    if (!empty($_FILES["ficha_pn"]["name"])) {

      $target_dir = "ficha_pn/";  

      $uniqueFileName = uniqid() . "-" . time();
      $fileExtension = pathinfo($_FILES["ficha_pn"]["name"], PATHINFO_EXTENSION);
      $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
      $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
      $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
      $check = getimagesize($_FILES["ficha_pn"]["tmp_name"]);

      if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

        if (move_uploaded_file($_FILES["ficha_pn"]["tmp_name"], $target_file)) {

          $update = $pdo -> query("UPDATE cc_subastas SET pn_ficha = '".$target_file."' WHERE id = '".$id_ult."'");
          
        } else {
          echo "Lo siento, ha ocurrido un error al subir tu archivo.";
        }
    
      } else {
        echo "El archivo no es una imagen.";
      }

    }

    // cc_pn

    if (!empty($_FILES["cc_pn"]["name"])) {

      $target_dir = "cc_pn/";  

      $uniqueFileName = uniqid() . "-" . time();
      $fileExtension = pathinfo($_FILES["cc_pn"]["name"], PATHINFO_EXTENSION);
      $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
      $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
      $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
      $check = getimagesize($_FILES["cc_pn"]["tmp_name"]);

      if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

        if (move_uploaded_file($_FILES["cc_pn"]["tmp_name"], $target_file)) {

          $update = $pdo -> query("UPDATE cc_subastas SET pn_cc = '".$target_file."' WHERE id = '".$id_ult."'");
          
        } else {
          echo "Lo siento, ha ocurrido un error al subir tu archivo.";
        }
    
      } else {
        echo "El archivo no es una imagen.";
      }

    }

    // carta_exo_pn

    if (!empty($_FILES["carta_exo_pn"]["name"])) {

      $target_dir = "carta_exo_pn/";  

      $uniqueFileName = uniqid() . "-" . time();
      $fileExtension = pathinfo($_FILES["carta_exo_pn"]["name"], PATHINFO_EXTENSION);
      $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
      $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
      $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
      $check = getimagesize($_FILES["carta_exo_pn"]["tmp_name"]);

      if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

        if (move_uploaded_file($_FILES["carta_exo_pn"]["tmp_name"], $target_file)) {

          $update = $pdo -> query("UPDATE cc_subastas SET pn_carta_ex = '".$target_file."' WHERE id = '".$id_ult."'");
          
        } else {
          echo "Lo siento, ha ocurrido un error al subir tu archivo.";
        }
    
      } else {
        echo "El archivo no es una imagen.";
      }

    }

} 

if (isset($_POST['nombre_completo_pni'])) {

  $insert = $pdo -> query("INSERT INTO cc_subastas(nombre_completo, email, telefono, tipo_persona, stat)VALUES('".$_POST['nombre_completo_pni']."', '".$_POST['email_pni']."', '".$_POST['telefono_pni']."', 'NATURAL INDEPENDIENTE', 1)");
  
  $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Registro Realizado!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
  
  $ultimo_id = $pdo -> query("SELECT MAX(id) as id FROM cc_subastas");
  $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);
  foreach ($rows as $row) {
    $id_ult = $row['id'];
  }

  // recibo

  if (!empty($_FILES["recibo_pni"]["name"])) {

    $target_dir = "recibo_pni/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["recibo_pni"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["recibo_pni"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["recibo_pni"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pni_servicios = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // cedula pasaporte

  if (!empty($_FILES["cedula_pni"]["name"])) {

    $target_dir = "cedula_pni/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["cedula_pni"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["cedula_pni"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["cedula_pni"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pni_cedula = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // aviso operaciones

  if (!empty($_FILES["aviso_pni"]["name"])) {

    $target_dir = "aviso_pni/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["aviso_pni"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["aviso_pni"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["aviso_pni"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pni_aviso_op = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // referencia

  if (!empty($_FILES["referencia_pni"]["name"])) {

    $target_dir = "referencia_pni/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["referencia_pni"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["referencia_pni"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["referencia_pni"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pni_referencia = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // conosca a su cliente

  if (!empty($_FILES["cc_pni"]["name"])) {

    $target_dir = "cc_pni/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["cc_pni"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["cc_pni"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["cc_pni"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pni_cc = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // carta 

  if (!empty($_FILES["carta_ex_pni"]["name"])) {

    $target_dir = "carta_ex_pni/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["carta_ex_pni"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["carta_ex_pni"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["carta_ex_pni"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pni_carta_ex = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

}

if (isset($_POST['nombre_completo_pj'])) {

  $insert = $pdo -> query("INSERT INTO cc_subastas(nombre_completo, email, telefono, tipo_persona, stat)VALUES('".$_POST['nombre_completo_pj']."', '".$_POST['email_pj']."', '".$_POST['telefono_pj']."', 'JURIDICA', 1)");
  
  $mensaje = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Registro Realizado!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
  
  $ultimo_id = $pdo -> query("SELECT MAX(id) as id FROM cc_subastas");
  $rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);
  foreach ($rows as $row) {
    $id_ult = $row['id'];
  }

  // registro publico

  if (!empty($_FILES["registro_publico_pj"]["name"])) {

    $target_dir = "registro_publico_pj/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["registro_publico_pj"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["registro_publico_pj"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["registro_publico_pj"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pj_registro_publico = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // cedula pasaporte

  if (!empty($_FILES["cedula_pj"]["name"])) {

    $target_dir = "cedula_pj/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["cedula_pj"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["cedula_pj"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["cedula_pj"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pj_cedula_pass = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // aviso operaciones

  if (!empty($_FILES["aviso_op_pj"]["name"])) {

    $target_dir = "aviso_op_pj/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["aviso_op_pj"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["aviso_op_pj"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["aviso_op_pj"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pj_aviso_ope = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // servicios

  if (!empty($_FILES["servicios_pj"]["name"])) {

    $target_dir = "servicios_pj/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["servicios_pj"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["servicios_pj"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["servicios_pj"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pj_servicios = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // conosca a su cliente

  if (!empty($_FILES["cc_pj"]["name"])) {

    $target_dir = "cc_pj/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["cc_pj"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["cc_pj"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["cc_pj"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pj_cc = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

  // carta 

  if (!empty($_FILES["carta_ex_pj"]["name"])) {

    $target_dir = "carta_ex_pj/";  

    $uniqueFileName = uniqid() . "-" . time();
    $fileExtension = pathinfo($_FILES["carta_ex_pj"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . $uniqueFileName . "." . $fileExtension;
    $allowed_image_extensions = ["jpg", "png", "gif", "bmp", "jpeg"];
    $allowed_doc_extensions = ["pdf", "doc", "docx", "txt"];
    $check = getimagesize($_FILES["carta_ex_pj"]["tmp_name"]);

    if ($check !== false || in_array($fileExtension, $allowed_doc_extensions)) {

      if (move_uploaded_file($_FILES["carta_ex_pj"]["tmp_name"], $target_file)) {

        $update = $pdo -> query("UPDATE cc_subastas SET pj_carta_exo = '".$target_file."' WHERE id = '".$id_ult."'");
        
      } else {
        echo "Lo siento, ha ocurrido un error al subir tu archivo.";
      }
  
    } else {
      echo "El archivo no es una imagen.";
    }

  }

}

?>

<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="/docs/5.3/assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.115.4">
    <title>Subastas - PCR</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/offcanvas-navbar/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://getbootstrap.com/docs/5.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
      <!-- Favicons -->
    <link rel="apple-touch-icon" href="/docs/5.3/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="manifest" href="/docs/5.3/assets/img/favicons/manifest.json">
    <link rel="mask-icon" href="/docs/5.3/assets/img/favicons/safari-pinned-tab.svg" color="#712cf9">
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon.ico">
    <meta name="theme-color" content="#712cf9">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }
      .bd-mode-toggle {
        z-index: 1500;
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="offcanvas-navbar.css" rel="stylesheet">
  </head>
  <body class="bg-body-tertiary">
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

    
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark" aria-label="Main navigation">
  <div class="container-fluid">
    <a class="navbar-brand" href="#"> <img src="logogrupopcr.png" width="150"> </a>

  </div>
</nav>



<main class="container">

  <br>
  <br>
  <br>
  <br>

  <?php echo $mensaje; ?>
    <p>Elija una de las tres opciones disponibles a continuación, seleccionando la que mejor se ajuste a su perfil: ya sea persona natural, 
      independiente o jurídica. Complete el formulario correspondiente y adjunte todos los documentos requeridos.<br>
      Se le notificará por correo electrónico una vez que su registro haya sido aprobado, permitiéndole así continuar con el proceso.<br></p>
  <div class="accordion accordion-flush" id="accordionFlushExample">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
        REQUISITOS PARA PERSONA NATURAL / ASALARIADO
      </button>
    </h2>
    <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
      <div class="accordion-body">

        <div class="my-3 p-3 bg-body rounded shadow-sm">
          <h6 class="border-bottom pb-2 mb-0">REQUISITOS</h6>
          <div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" role="img" aria-label="Placeholder: 32x32" focusable="false"><title>Recibo de Servicios básicos</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <p class="pb-3 mb-0 small lh-sm border-bottom">
              <strong class="d-block text-gray-dark">Recibo de Servicios básicos: Luz - Agua - Teléfono - Cable - etc. (En caso que no tenga su nombre, debe completar la carta de Declaración Jurada de domicilio)</strong>
            </p>
          </div>
          <div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" role="img" aria-label="Placeholder: 32x32" focusable="false"><title>Ficha o talonario o carta de trabajo</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <p class="pb-3 mb-0 small lh-sm border-bottom">
              <strong class="d-block text-gray-dark">Ficha o talonario o carta de trabajo</strong>
            </p>
          </div>
          <div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" role="img" aria-label="Placeholder: 32x32" focusable="false"><title>Conoce tu Cliente</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <p class="pb-3 mb-0 small lh-sm border-bottom">
              <strong class="d-block text-gray-dark">Llenar formulario Conoce tu Cliente</strong>
              Descargalo <a href="Panama Car Rental Persona Natural.xlsm" target="_blank" rel="noopener noreferrer"> aqui </a>, y llena todo el documento. <a href="Panama Car Rental Persona Natural.xlsm" target="_blank" rel="noopener noreferrer"> Descargar</a>
            </p>
          </div>
          <!--<div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" role="img" aria-label="Placeholder: 32x32" focusable="false"><title>Carta de exoneración de responsabilidad con firma y huella</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <p class="pb-3 mb-0 small lh-sm border-bottom">
              <strong class="d-block text-gray-dark">Carta de exoneración de responsabilidad con firma y huella</strong>
            </p>
          </div>-->
        </div>
        <form action="" method="post" enctype="multipart/form-data">
          <div class="my-3 p-3 bg-body rounded shadow-sm">
            <h5>Llene los campos y adjunte los documentos solicitados, los campos con (<span style="color:red;">*</span>) son obligatorios</h5>
            <label for=""> <b> Nombre Completo </b> <span style="color:red;">*</span></label>
            <input type="text" class="form-control" name="nombre_pn" required>
            <label for=""><b>Email </b><span style="color:red;">*</span></label>
            <input type="text" name="email_pn" class="form-control" required>
            <label for=""><b>N# Telefono </b><span style="color:red;">*</span></label>
            <input type="text" name="telefono_pn" class="form-control" required>
            <label for=""><b>Recibo de Servicios básicos </b><span style="color:red;">*</span></label>
            <input type="file" name="recibo_pn" required id=""class="form-control">
            <label for=""><b>Ficha, talonario o carta de trabajo </b><span style="color:red;">*</span></label>
            <input type="file" name="ficha_pn" id=""class="form-control" required>
            <label for=""><b>Formulario Conoce tu Cliente </b><span style="color:red;">*</span></label>
            <input type="file" name="cc_pn" id=""class="form-control" required>
            <!--<label for=""><b>Carta de exoneración </b></label>
            <input type="file" name="carta_exo_pn" name="" id=""class="form-control">-->
            <br>
            <input type="submit" value="Enviar Informacion" class="btn btn-primary" id="enviar_pn" name="perosna_pn">
          </div>
        </form>

      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
        REQUISITOS PARA INDEPENDIENTE FORMAL / PERSONA NATURAL
      </button>
    </h2>
    <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
      <div class="accordion-body">

      <div class="my-3 p-3 bg-body rounded shadow-sm">
        <h6 class="border-bottom pb-2 mb-0">REQUISITOS</h6>
        <div class="d-flex text-body-secondary pt-3">
          <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
          <p class="pb-3 mb-0 small lh-sm border-bottom">
            <strong class="d-block text-gray-dark">Copia de cédula o Pasaporte</strong>
          </p>
        </div>
        <div class="d-flex text-body-secondary pt-3">
          <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
          <p class="pb-3 mb-0 small lh-sm border-bottom">
            <strong class="d-block text-gray-dark">Aviso de Operaciones vigente</strong>
          </p>
        </div>
        <div class="d-flex text-body-secondary pt-3">
          <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
          <p class="pb-3 mb-0 small lh-sm border-bottom">
            <strong class="d-block text-gray-dark">Recibo de Servicios básicos: Luz - Agua - Teléfono - Cable - etc. (En caso que no tenga su nombre, debe completar la carta de Declaración Jurada de domicilio)</strong>
          </p>
        </div>
        <div class="d-flex text-body-secondary pt-3">
          <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
          <p class="pb-3 mb-0 small lh-sm border-bottom">
            <strong class="d-block text-gray-dark">Carta de referencia bancaria a nombre de la persona justificando que mantiene un producto y que cuenta con fondos disponibles. No se aceptan cifras bajas. </strong>
          </p>
        </div>
        <div class="d-flex text-body-secondary pt-3">
          <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
          <p class="pb-3 mb-0 small lh-sm border-bottom">
            <strong class="d-block text-gray-dark">Llenar formulario Conoce tu Cliente </strong>
            Descargalo <a href="Panama Car Rental Persona Natural.xlsm" target="_blank" rel="noopener noreferrer"> aqui </a>, y llena todo el documento. <a href="Panama Car Rental Persona Natural.xlsm" target="_blank" rel="noopener noreferrer"> Descargar</a>
          </p>
        </div>
        <!--<div class="d-flex text-body-secondary pt-3">
          <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
          <p class="pb-3 mb-0 small lh-sm border-bottom">
            <strong class="d-block text-gray-dark">Carta de exoneración de responsabilidad con firma y huella</strong>
          </p>
        </div> -->
      </div>

      <form action="" method="post" enctype="multipart/form-data">
        <div class="my-3 p-3 bg-body rounded shadow-sm">
          <h5>Llene los campos y adjunte los documentos solicitados, los campos con (<span style="color:red;">*</span>) son obligatorios</h5>
          <label for=""><b>Nombre Completo </b><span style="color:red;">*</span></label>
          <input type="text" name="nombre_completo_pni" class="form-control" value="">
          <label for=""><b>Email </b><span style="color:red;">*</span></label>
          <input type="text" name="email_pni" class="form-control">
          <label for=""><b>N# Telefono </b><span style="color:red;">*</span></label>
          <input type="text" name="telefono_pni" class="form-control" required>
          <label for=""><b>Copia de cédula o Pasaporte </b><span style="color:red;">*</span></label>
          <input type="file" name="cedula_pni" id=""class="form-control">
          <label for=""><b>Aviso de Operaciones vigente </b><span style="color:red;">*</span></label>
          <input type="file" name="aviso_pni" id=""class="form-control">
          <label for=""><b>Recibo de Servicios básicos</b><span style="color:red;">*</span></label>
          <input type="file" name="recibo_pni" id=""class="form-control">
          <label for=""><b>Carta de referencia bancaria</b><span style="color:red;">*</span></label>
          <input type="file" name="referencia_pni" id=""class="form-control">
          <label for=""><b>Formulario Conoce tu Cliente</b><span style="color:red;">*</span></label>
          <input type="file" name="cc_pni" id=""class="form-control">
          <!--<label for=""><b>Carta de exoneración</b></label>
          <input type="file" name="carta_ex_pni" id=""class="form-control">-->
          <br>
          <input type="submit" value="Enviar Informacion" class="btn btn-primary" id="enviar_pni" name="pesona_pni">
        </div>
      </form>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
        REQUISITOS PARA JURIDICO NACIONAL O EXTRANJERO
      </button>
    </h2>
    <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
      <div class="accordion-body">

        <div class="my-3 p-3 bg-body rounded shadow-sm">
          <h6 class="border-bottom pb-2 mb-0">REQUISITOS</h6>
          <div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
              <div class="d-flex justify-content-between">
                <strong class="text-gray-dark">Certificado de Registro Público. Vigencia mínima 3 meses (Excepción: Print de pantalla del Registro Público) o su equivalente en su país. </strong>
              </div>
            </div>
          </div>
          <div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
              <div class="d-flex justify-content-between">
                <strong class="text-gray-dark">Aviso de operaciones vigente. Copia de cédula del representante legal. </strong>
              </div>
            </div>
          </div>
          <div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
              <div class="d-flex justify-content-between">
                <strong class="text-gray-dark">Copia de cédula o pasaporte del representante legal. </strong>
              </div>
            </div>
          </div>
          <div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
              <div class="d-flex justify-content-between">
                <strong class="text-gray-dark">Recibo de Servicios básicos: Luz - Agua - Teléfono - Cable - etc. (En caso que no tenga su nombre, debe completar la carta de Declaración Jurada de domicilio). </strong>
              </div>
            </div>
          </div>
          <div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
              <div class="d-flex justify-content-between">
                <strong class="text-gray-dark">Llenar formulario Conoce tu Cliente. </strong>
              </div>
              Descargalo <a href="Panama Car Rental Persona Jurídica.xlsm" target="_blank" rel="noopener noreferrer"> aqui </a>, y llena todo el documento. <a href="Panama Car Rental Persona Jurídica.xlsm" target="_blank" rel="noopener noreferrer"> Descargar</a>
            </div>
          </div>
          <!--<div class="d-flex text-body-secondary pt-3">
            <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: 32x32" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#007bff"/><text x="50%" y="50%" fill="#007bff" dy=".3em">32x32</text></svg>
            <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
              <div class="d-flex justify-content-between">
                <strong class="text-gray-dark">Carta de exoneración de responsabilidad con firma y huella. </strong>
              </div>
            </div>
          </div>-->
        </div>
        <form action="" method="post" enctype="multipart/form-data">
          <div class="my-3 p-3 bg-body rounded shadow-sm">
            <h5>Llene los campos y adjunte los documentos solicitados, los campos con (<span style="color:red;">*</span>) son obligatorios</h5>
            <label for=""><b>Nombre Completo </b><span style="color:red;">*</span></label>
            <input type="text" class="form-control" name="nombre_completo_pj">
            <label for=""><b>Email </b><span style="color:red;">*</span></label>
            <input type="text" name="email_pj" class="form-control">
            <label for=""><b>N# Telefono </b><span style="color:red;">*</span></label>
            <input type="text" name="telefono_pj" class="form-control" required>
            <label for=""><b>Certificado de Registro Público </b><span style="color:red;">*</span></label>
            <input type="file" name="registro_publico_pj" id=""class="form-control">
            <label for=""><b>Aviso de Operaciones vigente </b><span style="color:red;">*</span></label>
            <input type="file" name="aviso_op_pj" id=""class="form-control">
            <label for=""><b>Copia de cédula o pasaport</b><span style="color:red;">*</span></label>
            <input type="file" name="cedula_pj" id=""class="form-control">
            <label for=""><b>Recibo de Servicios</b><span style="color:red;">*</span></label>
            <input type="file" name="servicios_pj" id=""class="form-control">
            <label for=""><b>Formulario Conoce tu Cliente</b><span style="color:red;">*</span></label>
            <input type="file" name="cc_pj" id=""class="form-control">
            <!--<label for=""><b>Carta de exoneración</b></label>
            <input type="file" name="carta_ex_pj" id=""class="form-control">-->
            <br>
            <input type="submit" value="Enviar Informacion" class="btn btn-primary" id="enviar_pj" name="persona_pj">
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

</main>
    <script src="https://getbootstrap.com/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script src="https://getbootstrap.com/docs/5.3/examples/offcanvas-navbar/offcanvas-navbar.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const btnEnviar = document.getElementById('enviar_pn');
        const btnEnviarpni = document.getElementById('enviar_pni');
        const btnEnviarpj = document.getElementById('enviar_pj');

        form.addEventListener('submit', function(event) {

          btnEnviar.disabled = true;
          btnEnviar.value = 'Enviando...'; 
          btnEnviarpni.disabled = true;
          btnEnviarpni.value = 'Enviando...'; 
          btnEnviarpj.disabled = true;
          btnEnviarpj.value = 'Enviando...'; 
        });
      });
    </script>

  </body>
</html>
