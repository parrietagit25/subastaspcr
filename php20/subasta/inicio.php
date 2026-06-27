<?php 
session_start();
$mensaje = "";
require 'vendor/autoload.php';

if(!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

try {
  $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Error de conexiÃ³n: " . $e->getMessage();
}

// Obtener fechas por defecto (Ãºltimo mes)
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-01');
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : date('Y-m-t');

// FunciÃ³n para obtener estadÃ­sticas
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

// Obtener estadÃ­sticas por mes
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

// Obtener estadÃ­sticas por semana
function obtenerEstadisticasPorSemana($pdo, $fecha_inicio, $fecha_fin) {
    $query = "SELECT 
        YEAR(date_time) as anio,
        WEEK(date_time) as semana,
        COUNT(*) as total,
        SUM(CASE WHEN stat = 1 THEN 1 ELSE 0 END) as pendientes,
        SUM(CASE WHEN stat = 2 THEN 1 ELSE 0 END) as aprobadas,
        SUM(CASE WHEN stat = 3 THEN 1 ELSE 0 END) as eliminadas,
        SUM(CASE WHEN stat = 4 THEN 1 ELSE 0 END) as supervisor
        FROM cc_subastas 
        WHERE date_time BETWEEN ? AND ?
        GROUP BY YEAR(date_time), WEEK(date_time)
        ORDER BY anio, semana";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include('includes/admin_assets.php'); ?>
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

    
  </head>
  <body class="admin-body">
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
              <table id="detalleTable" class="table table-hover pcr-table w-100">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Tipo Persona</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>TelÃ©fono</th>
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

    <main class="admin-main">
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

          <!-- Tarjetas de estadÃ­sticas -->
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

          <!-- GrÃ¡ficas -->
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title mb-0">EstadÃ­sticas por Mes</h5>
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
                  <h5 class="card-title mb-0">EstadÃ­sticas por Semana</h5>
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

    <?php include('includes/admin_layout_close.php'); ?>
    <script>
      // Datos para las grÃ¡ficas
      const datosMes = <?php echo json_encode($estadisticas_mes); ?>;
      const datosSemana = <?php echo json_encode($estadisticas_semana); ?>;

      // GrÃ¡fica por mes
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

      // GrÃ¡fica por semana
      const ctxSemana = document.getElementById('chartSemana').getContext('2d');
      new Chart(ctxSemana, {
        type: 'bar',
        data: {
          labels: datosSemana.map(item => `Año ${item.anio} - Semana ${item.semana}`),
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

      // FunciÃ³n para filtrar dashboard
      function filtrarDashboard() {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        
        if (fechaInicio && fechaFin) {
          window.location.href = `inicio.php?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
        } else {
          alert('Por favor seleccione ambas fechas');
        }
      }

      // FunciÃ³n para mostrar detalles
      function mostrarDetalle(tipo) {
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        
        // Determinar el estado segÃºn el tipo
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

        // Actualizar tÃ­tulo del modal
        document.getElementById('detalleModalLabel').textContent = titulo;

        // Cargar datos
        fetch(`consultas_dashboard.php?tipo=${tipo}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
          .then(response => response.json())
          .then(data => {
            const tbody = document.querySelector('#detalleTable tbody');
            tbody.innerHTML = '';
            
            data.forEach(item => {
              const estadoTexto = estadoSolicitudHtml(parseInt(item.stat, 10));
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
        return estadoSolicitudHtml(stat);
      }

      // Inicializar DataTable cuando se abra el modal
      document.getElementById('detalleModal').addEventListener('shown.bs.modal', function () {
        $('#detalleTable').DataTable({
          language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
          }
        });
      });

      // Recargar pÃ¡gina cuando se cierre el modal
      document.getElementById('detalleModal').addEventListener('hidden.bs.modal', function () {
        location.reload();
      });
    </script>
  </body>
</html>
