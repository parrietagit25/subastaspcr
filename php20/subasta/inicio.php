<?php 
session_start();
$mensaje = "";
require 'vendor/autoload.php';
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

// Obtener fechas por defecto (último mes)
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

// Función para obtener estadísticas
function obtenerEstadisticas($pdo, $fecha_inicio, $fecha_fin) {
    $stats = [];
    
    // Total general
    $query = "SELECT COUNT(*) as total FROM cc_subastas WHERE date_time BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
    $stats['total'] = $stmt->fetch()['total'];
    
    // Pendientes
    $query = "SELECT COUNT(*) as total FROM cc_subastas WHERE stat = 1 AND date_time BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
    $stats['pendientes'] = $stmt->fetch()['total'];
    
    // Aprobadas
    $query = "SELECT COUNT(*) as total FROM cc_subastas WHERE stat = 2 AND date_time BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
    $stats['aprobadas'] = $stmt->fetch()['total'];
    
    // Eliminadas
    $query = "SELECT COUNT(*) as total FROM cc_subastas WHERE stat = 3 AND date_time BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
    $stats['eliminadas'] = $stmt->fetch()['total'];
    
    // Enviadas al supervisor
    $query = "SELECT COUNT(*) as total FROM cc_subastas WHERE stat = 4 AND date_time BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
    $stats['supervisor'] = $stmt->fetch()['total'];
    
    // Total de adjuntos
    $query = "SELECT 
        SUM(CASE WHEN pn_recibo_servicios IS NOT NULL AND pn_recibo_servicios != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pn_ficha IS NOT NULL AND pn_ficha != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pn_carta_ex IS NOT NULL AND pn_carta_ex != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pn_contrato IS NOT NULL AND pn_contrato != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pn_cc IS NOT NULL AND pn_cc != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pni_cedula IS NOT NULL AND pni_cedula != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pni_aviso_op IS NOT NULL AND pni_aviso_op != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pni_servicios IS NOT NULL AND pni_servicios != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pni_referencia IS NOT NULL AND pni_referencia != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pni_cc IS NOT NULL AND pni_cc != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pni_carta_ex IS NOT NULL AND pni_carta_ex != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pj_registro_publico IS NOT NULL AND pj_registro_publico != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pj_aviso_ope IS NOT NULL AND pj_aviso_ope != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pj_cedula_pass IS NOT NULL AND pj_cedula_pass != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pj_servicios IS NOT NULL AND pj_servicios != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pj_cc IS NOT NULL AND pj_cc != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pj_carta_exo IS NOT NULL AND pj_carta_exo != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pj_contrato IS NOT NULL AND pj_contrato != '' THEN 1 ELSE 0 END) +
        SUM(CASE WHEN pni_contrato IS NOT NULL AND pni_contrato != '' THEN 1 ELSE 0 END) as total_adjuntos
        FROM cc_subastas WHERE date_time BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
    $stats['total_adjuntos'] = $stmt->fetch()['total_adjuntos'] ?? 0;
    
    // Promedio de adjuntos
    $stats['promedio_adjuntos'] = $stats['total'] > 0 ? round($stats['total_adjuntos'] / $stats['total'], 2) : 0;
    
    return $stats;
}

// Obtener estadísticas por mes
function obtenerEstadisticasPorMes($pdo, $fecha_inicio, $fecha_fin) {
    $query = "SELECT 
        DATE_FORMAT(date_time, '%Y-%m') as mes,
        COUNT(*) as total,
        SUM(CASE WHEN stat = 1 THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN stat = 2 THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN stat = 3 THEN 1 ELSE 0 END) as eliminadas,
        SUM(CASE WHEN stat = 4 THEN 1 ELSE 0 END) as supervisor
        FROM cc_subastas 
        WHERE date_time BETWEEN ? AND ?
        GROUP BY DATE_FORMAT(date_time, '%Y-%m')
        ORDER BY mes";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener estadísticas por semana
function obtenerEstadisticasPorSemana($pdo, $fecha_inicio, $fecha_fin) {
    $query = "SELECT 
        YEAR(date_time) as año,
        WEEK(date_time) as semana,
        COUNT(*) as total,
        SUM(CASE WHEN stat = 1 THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN stat = 2 THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN stat = 3 THEN 1 ELSE 0 END) as eliminadas,
        SUM(CASE WHEN stat = 4 THEN 1 ELSE 0 END) as supervisor
        FROM cc_subastas 
        WHERE date_time BETWEEN ? AND ?
        GROUP BY YEAR(date_time), WEEK(date_time)
        ORDER BY año, semana";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$fecha_inicio . ' 00:00:00', $fecha_fin . ' 23:59:59']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$estadisticas = obtenerEstadisticas($pdo, $fecha_inicio, $fecha_fin);
$estadisticas_mes = obtenerEstadisticasPorMes($pdo, $fecha_inicio, $fecha_fin);
$estadisticas_semana = obtenerEstadisticasPorSemana($pdo, $fecha_inicio, $fecha_fin);

?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head><script src="https://getbootstrap.com/docs/5.3/assets/js/color-modes.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.115.4">
    <title>Dashboard - Subastas PCR</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">
    <link href="https://getbootstrap.com/docs/5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

      .dashboard-card {
        cursor: pointer;
        transition: transform 0.2s;
        height: 120px;
      }

      .dashboard-card:hover {
        transform: translateY(-5px);
      }

      .chart-container {
        height: 400px;
        width: 100%;
      }

      .stats-card {
        border-left: 4px solid;
      }

      .stats-card.pendientes {
        border-left-color: #ffc107;
      }

      .stats-card.aprobadas {
        border-left-color: #198754;
      }

      .stats-card.eliminadas {
        border-left-color: #dc3545;
      }

      .stats-card.supervisor {
        border-left-color: #0d6efd;
      }

      .stats-card.total {
        border-left-color: #6f42c1;
      }

      .stats-card.adjuntos {
        border-left-color: #fd7e14;
      }

    </style>

    
    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/5.3/examples/sign-in/sign-in.css" rel="stylesheet">
  </head>
  <body class="d-flex align-items-center py-4 bg-body-tertiary">
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
        <path d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 .707 0z"/>
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

    <!-- Modal para detalles -->
    <div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="detalleModalLabel">Detalle de Registros</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table id="detalleTable" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Tipo Persona</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <main class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Dashboard - Subastas PCR</h2>
            <div class="d-flex gap-2">
              <input type="date" class="form-control" id="fecha_inicio" value="<?php echo $fecha_inicio; ?>" style="width: auto;">
              <input type="date" class="form-control" id="fecha_fin" value="<?php echo $fecha_fin; ?>" style="width: auto;">
              <button class="btn btn-primary" onclick="filtrarDashboard()">Filtrar</button>
            </div>
          </div>

          <!-- Tarjetas de estadísticas -->
          <div class="row mb-4">
            <div class="col-md-2">
              <div class="card stats-card total dashboard-card" onclick="mostrarDetalle('total')">
                <div class="card-body text-center">
                  <h5 class="card-title text-primary">Total General</h5>
                  <h2 class="text-primary"><?php echo $estadisticas['total']; ?></h2>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stats-card pendientes dashboard-card" onclick="mostrarDetalle('pendientes')">
                <div class="card-body text-center">
                  <h5 class="card-title text-warning">Pendientes</h5>
                  <h2 class="text-warning"><?php echo $estadisticas['pendientes']; ?></h2>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stats-card aprobadas dashboard-card" onclick="mostrarDetalle('aprobadas')">
                <div class="card-body text-center">
                  <h5 class="card-title text-success">Aprobadas</h5>
                  <h2 class="text-success"><?php echo $estadisticas['aprobadas']; ?></h2>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stats-card supervisor dashboard-card" onclick="mostrarDetalle('supervisor')">
                <div class="card-body text-center">
                  <h5 class="card-title text-info">Enviadas al Supervisor</h5>
                  <h2 class="text-info"><?php echo $estadisticas['supervisor']; ?></h2>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stats-card eliminadas dashboard-card" onclick="mostrarDetalle('eliminadas')">
                <div class="card-body text-center">
                  <h5 class="card-title text-danger">Eliminadas</h5>
                  <h2 class="text-danger"><?php echo $estadisticas['eliminadas']; ?></h2>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="card stats-card adjuntos dashboard-card" onclick="mostrarDetalle('adjuntos')">
                <div class="card-body text-center">
                  <h5 class="card-title text-warning">Total Adjuntos</h5>
                  <h2 class="text-warning"><?php echo $estadisticas['total_adjuntos']; ?></h2>
                  <small class="text-muted">Promedio: <?php echo $estadisticas['promedio_adjuntos']; ?></small>
                </div>
              </div>
            </div>
          </div>

          <!-- Gráficas -->
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">Estadísticas por Mes</h5>
                </div>
                <div class="card-body">
                  <div class="chart-container">
                    <canvas id="chartMes"></canvas>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">Estadísticas por Semana</h5>
                </div>
                <div class="card-body">
                  <div class="chart-container">
                    <canvas id="chartSemana"></canvas>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <script src="https://getbootstrap.com/docs/5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Datos para las gráficas
      const datosMes = <?php echo json_encode($estadisticas_mes); ?>;
      const datosSemana = <?php echo json_encode($estadisticas_semana); ?>;

      // Gráfica por mes
      const ctxMes = document.getElementById('chartMes').getContext('2d');
      new Chart(ctxMes, {
        type: 'line',
        data: {
          labels: datosMes.map(item => item.mes),
          datasets: [
            {
              label: 'Total',
              data: datosMes.map(item => item.total),
              borderColor: '#6f42c1',
              backgroundColor: 'rgba(111, 66, 193, 0.1)',
              tension: 0.1
            },
            {
              label: 'Pendientes',
              data: datosMes.map(item => item.pendientes),
              borderColor: '#ffc107',
              backgroundColor: 'rgba(255, 193, 7, 0.1)',
              tension: 0.1
            },
            {
              label: 'Aprobadas',
              data: datosMes.map(item => item.aprobadas),
              borderColor: '#198754',
              backgroundColor: 'rgba(25, 135, 84, 0.1)',
              tension: 0.1
            },
            {
              label: 'Eliminadas',
              data: datosMes.map(item => item.eliminadas),
              borderColor: '#dc3545',
              backgroundColor: 'rgba(220, 53, 69, 0.1)',
              tension: 0.1
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      // Gráfica por semana
      const ctxSemana = document.getElementById('chartSemana').getContext('2d');
      new Chart(ctxSemana, {
        type: 'bar',
        data: {
          labels: datosSemana.map(item => `Año ${item.año} - Semana ${item.semana}`),
          datasets: [
            {
              label: 'Total',
              data: datosSemana.map(item => item.total),
              backgroundColor: '#6f42c1'
            },
            {
              label: 'Pendientes',
              data: datosSemana.map(item => item.pendientes),
              backgroundColor: '#ffc107'
            },
            {
              label: 'Aprobadas',
              data: datosSemana.map(item => item.aprobadas),
              backgroundColor: '#198754'
            },
            {
              label: 'Eliminadas',
              data: datosSemana.map(item => item.eliminadas),
              backgroundColor: '#dc3545'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      // Función para filtrar dashboard
      function filtrarDashboard() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        
        if (fechaInicio && fechaFin) {
          window.location.href = `inicio.php?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        } else {
          alert('Por favor seleccione ambas fechas');
        }
      }

      // Función para mostrar detalles
      function mostrarDetalle(tipo) {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        
        // Determinar el estado según el tipo
        let estado = '';
        let titulo = '';

        console.log(tipo);
        
        switch(tipo) {
          case 'total':
            estado = '';
            titulo = 'Todos los Registros';
            break;
          case 'pendientes':
            estado = '1';
            titulo = 'Registros Pendientes';
            break;
          case 'aprobadas':
            estado = '2';
            titulo = 'Registros Aprobados';
            break;
          case 'supervisor':
            estado = '4';
            titulo = 'Registros Enviados al Supervisor';
            break;
          case 'eliminadas':
            estado = '3';
            titulo = 'Registros Eliminados';
            break;
          case 'adjuntos':
            estado = 'adjuntos';
            titulo = 'Registros con Adjuntos';
            break;
        }

        console.log(estado);
        console.log(titulo);
        console.log(tipo);

        // Actualizar título del modal
        document.getElementById('detalleModalLabel').textContent = titulo;

        // Cargar datos
        fetch(`consultas_dashboard.php?tipo=${tipo}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
          .then(response => response.json())
          .then(data => {
            const tbody = document.querySelector('#detalleTable tbody');
            tbody.innerHTML = '';
            
            data.forEach(item => {
              const estadoTexto = getEstadoTexto(item.stat);
              const row = `
                <tr>
                  <td>${item.id}</td>
                  <td>${item.tipo_persona}</td>
                  <td>${item.nombre_completo}</td>
                  <td>${item.email}</td>
                  <td>${item.telefono}</td>
                  <td>${item.date_time}</td>
                  <td>${estadoTexto}</td>
                </tr>
              `;
              tbody.innerHTML += row;
            });

            // Mostrar modal
            new bootstrap.Modal(document.getElementById('detalleModal')).show();
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos');
          });
      }

      function getEstadoTexto(stat) {
        switch(stat) {
          case '1': return 'Pendiente';
          case '2': return 'Aprobado';
          case '3': return 'Eliminado';
          case '4': return 'Enviado al Supervisor';
          default: return 'Desconocido';
        }
      }

      // Inicializar DataTable cuando se abra el modal
      document.getElementById('detalleModal').addEventListener('shown.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#detalleTable')) {
          $('#detalleTable').DataTable().destroy();
        }
        $('#detalleTable').DataTable({
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
          }
        });
      });
    </script>
  </body>
</html>
