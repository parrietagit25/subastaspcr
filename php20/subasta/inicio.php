<?php 
session_start();
$mensaje = "";

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

// Consultas para el dashboard
// Total de solicitudes
$total_solicitudes = $pdo->query("SELECT COUNT(*) as total FROM cc_subastas")->fetch()['total'];

// Solicitudes por estado
$pendientes = $pdo->query("SELECT COUNT(*) as total FROM cc_subastas WHERE stat = 1")->fetch()['total'];
$aprobadas = $pdo->query("SELECT COUNT(*) as total FROM cc_subastas WHERE stat = 2")->fetch()['total'];
$eliminadas = $pdo->query("SELECT COUNT(*) as total FROM cc_subastas WHERE stat = 3")->fetch()['total'];
$enviadas_supervisor = $pdo->query("SELECT COUNT(*) as total FROM cc_subastas WHERE stat = 4")->fetch()['total'];

// Total de usuarios registrados
$total_usuarios = $pdo->query("SELECT COUNT(*) as total FROM usuarios")->fetch()['total'];

// Solicitudes por tipo de persona
$natural = $pdo->query("SELECT COUNT(*) as total FROM cc_subastas WHERE tipo_persona = 'NATURAL'")->fetch()['total'];
$natural_independiente = $pdo->query("SELECT COUNT(*) as total FROM cc_subastas WHERE tipo_persona = 'NATURAL INDEPENDIENTE'")->fetch()['total'];
$juridica = $pdo->query("SELECT COUNT(*) as total FROM cc_subastas WHERE tipo_persona = 'JURIDICA'")->fetch()['total'];

// Estadísticas por mes (últimos 6 meses)
$estadisticas_mes = $pdo->query("
    SELECT 
        DATE_FORMAT(date_time, '%Y-%m') as mes,
        COUNT(*) as total,
        SUM(CASE WHEN stat = 1 THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN stat = 2 THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN stat = 3 THEN 1 ELSE 0 END) as eliminadas
    FROM cc_subastas 
    WHERE date_time >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(date_time, '%Y-%m')
    ORDER BY mes DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas por semana (últimas 4 semanas)
$estadisticas_semana = $pdo->query("
    SELECT 
        YEARWEEK(date_time) as semana,
        COUNT(*) as total,
        SUM(CASE WHEN stat = 1 THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN stat = 2 THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN stat = 3 THEN 1 ELSE 0 END) as eliminadas
    FROM cc_subastas 
    WHERE date_time >= DATE_SUB(NOW(), INTERVAL 4 WEEK)
    GROUP BY YEARWEEK(date_time)
    ORDER BY semana DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Calcular total de adjuntos y promedio
$total_adjuntos = 0;
$solicitudes_con_adjuntos = 0;

$solicitudes = $pdo->query("SELECT * FROM cc_subastas")->fetchAll(PDO::FETCH_ASSOC);
foreach ($solicitudes as $solicitud) {
    $adjuntos_count = 0;
    
    // Contar adjuntos según tipo de persona
    if ($solicitud['tipo_persona'] == 'NATURAL') {
        $campos = ['pn_recibo_servicios', 'pn_ficha', 'pn_cc', 'pn_cedula'];
    } elseif ($solicitud['tipo_persona'] == 'NATURAL INDEPENDIENTE') {
        $campos = ['pni_cedula', 'pni_aviso_op', 'pni_servicios', 'pni_referencia', 'pni_cc'];
    } elseif ($solicitud['tipo_persona'] == 'JURIDICA') {
        $campos = ['pj_registro_publico', 'pj_aviso_ope', 'pj_cedula_pass', 'pj_servicios', 'pj_cc'];
    }
    
    foreach ($campos as $campo) {
        if (!empty($solicitud[$campo])) {
            $adjuntos_count++;
        }
    }
    
    if ($adjuntos_count > 0) {
        $total_adjuntos += $adjuntos_count;
        $solicitudes_con_adjuntos++;
    }
}

$promedio_adjuntos = $solicitudes_con_adjuntos > 0 ? round($total_adjuntos / $solicitudes_con_adjuntos, 2) : 0;

// Solicitudes recientes (últimas 5)
$solicitudes_recientes = $pdo->query("
    SELECT id, nombre_completo, email, tipo_persona, stat, date_time 
    FROM cc_subastas 
    ORDER BY date_time DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

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

      .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
      }

      .stat-card:hover {
        transform: translateY(-5px);
      }

      .stat-card.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      }

      .stat-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      }

      .stat-card.danger {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        color: #333;
      }

      .stat-card.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      }

      .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
      }

      .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
      }

      .chart-container {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      }

      .recent-table {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      }

      .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
      }

      .status-pendiente {
        background-color: #fff3cd;
        color: #856404;
      }

      .status-aprobado {
        background-color: #d4edda;
        color: #155724;
      }

      .status-eliminado {
        background-color: #f8d7da;
        color: #721c24;
      }

      .status-supervisor {
        background-color: #d1ecf1;
        color: #0c5460;
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
    
<main class="container-fluid">
    <div class="container">
        <?php echo $mensaje; ?>
        
        <!-- Header del Dashboard -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-4 fw-bold text-center mb-0">Dashboard Subastas PCR</h1>
                <p class="text-center text-muted">Panel de control y estadísticas del sistema</p>
            </div>
        </div>

        <!-- Tarjetas de estadísticas principales -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($total_solicitudes); ?></div>
                    <div class="stat-label">Total de Solicitudes</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="stat-number"><?php echo number_format($aprobadas); ?></div>
                    <div class="stat-label">Aprobadas</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="stat-number"><?php echo number_format($pendientes); ?></div>
                    <div class="stat-label">Pendientes</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card danger">
                    <div class="stat-number"><?php echo number_format($eliminadas); ?></div>
                    <div class="stat-label">Eliminadas</div>
                </div>
            </div>
        </div>

        <!-- Segunda fila de estadísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card info">
                    <div class="stat-number"><?php echo number_format($total_usuarios); ?></div>
                    <div class="stat-label">Usuarios Registrados</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($total_adjuntos); ?></div>
                    <div class="stat-label">Total de Adjuntos</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card success">
                    <div class="stat-number"><?php echo $promedio_adjuntos; ?></div>
                    <div class="stat-label">Promedio de Adjuntos</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stat-card warning">
                    <div class="stat-number"><?php echo number_format($enviadas_supervisor); ?></div>
                    <div class="stat-label">Enviadas al Supervisor</div>
                </div>
            </div>
        </div>

        <!-- Gráficos y tablas -->
        <div class="row mb-4">
            <!-- Gráfico de solicitudes por tipo de persona -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h5 class="mb-3">Solicitudes por Tipo de Persona</h5>
                    <canvas id="tipoPersonaChart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <!-- Gráfico de estado de solicitudes -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h5 class="mb-3">Estado de las Solicitudes</h5>
                    <canvas id="estadoChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de tendencias mensuales -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">Tendencias Mensuales (Últimos 6 Meses)</h5>
                    <canvas id="tendenciasChart" width="800" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla de solicitudes recientes -->
        <div class="row">
            <div class="col-12">
                <div class="recent-table">
                    <h5 class="mb-3">Solicitudes Recientes</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($solicitudes_recientes as $solicitud): ?>
                                <tr>
                                    <td><?php echo $solicitud['id']; ?></td>
                                    <td><?php echo htmlspecialchars($solicitud['nombre_completo']); ?></td>
                                    <td><?php echo htmlspecialchars($solicitud['email']); ?></td>
                                    <td><?php echo $solicitud['tipo_persona']; ?></td>
                                    <td>
                                        <?php
                                        $estado = '';
                                        $clase = '';
                                        switch($solicitud['stat']) {
                                            case 1:
                                                $estado = 'Pendiente';
                                                $clase = 'status-pendiente';
                                                break;
                                            case 2:
                                                $estado = 'Aprobado';
                                                $clase = 'status-aprobado';
                                                break;
                                            case 3:
                                                $estado = 'Eliminado';
                                                $clase = 'status-eliminado';
                                                break;
                                            case 4:
                                                $estado = 'Enviado al Supervisor';
                                                $clase = 'status-supervisor';
                                                break;
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $clase; ?>"><?php echo $estado; ?></span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($solicitud['date_time'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://getbootstrap.com/docs/5.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Gráfico de tipo de persona
const tipoPersonaCtx = document.getElementById('tipoPersonaChart').getContext('2d');
new Chart(tipoPersonaCtx, {
    type: 'doughnut',
    data: {
        labels: ['Persona Natural', 'Natural Independiente', 'Persona Jurídica'],
        datasets: [{
            data: [<?php echo $natural; ?>, <?php echo $natural_independiente; ?>, <?php echo $juridica; ?>],
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de estado de solicitudes
const estadoCtx = document.getElementById('estadoChart').getContext('2d');
new Chart(estadoCtx, {
    type: 'pie',
    data: {
        labels: ['Aprobadas', 'Pendientes', 'Eliminadas', 'Enviadas al Supervisor'],
        datasets: [{
            data: [<?php echo $aprobadas; ?>, <?php echo $pendientes; ?>, <?php echo $eliminadas; ?>, <?php echo $enviadas_supervisor; ?>],
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#dc3545',
                '#17a2b8'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de tendencias mensuales
const tendenciasCtx = document.getElementById('tendenciasChart').getContext('2d');
const meses = <?php echo json_encode(array_column($estadisticas_mes, 'mes')); ?>;
const totales = <?php echo json_encode(array_column($estadisticas_mes, 'total')); ?>;
const pendientes = <?php echo json_encode(array_column($estadisticas_mes, 'pendientes')); ?>;
const aprobadas = <?php echo json_encode(array_column($estadisticas_mes, 'aprobadas')); ?>;
const eliminadas = <?php echo json_encode(array_column($estadisticas_mes, 'eliminadas')); ?>;

new Chart(tendenciasCtx, {
    type: 'line',
    data: {
        labels: meses,
        datasets: [{
            label: 'Total',
            data: totales,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4
        }, {
            label: 'Pendientes',
            data: pendientes,
            borderColor: '#ffc107',
            backgroundColor: 'rgba(255, 193, 7, 0.1)',
            tension: 0.4
        }, {
            label: 'Aprobadas',
            data: aprobadas,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
        }, {
            label: 'Eliminadas',
            data: eliminadas,
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});
</script>
    </body>
</html>
