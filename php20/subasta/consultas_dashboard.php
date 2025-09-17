<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION["email"])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

try {
    $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit();
}

$tipo = $_GET['tipo'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');

$fecha_inicio .= ' 00:00:00';
$fecha_fin .= ' 23:59:59';

$query = "SELECT id, tipo_persona, nombre_completo, email, telefono, date_time, stat 
          FROM cc_subastas 
          WHERE date_time BETWEEN ? AND ?";

$params = [$fecha_inicio, $fecha_fin];

// Agregar filtro por estado si no es 'total'
if ($tipo !== 'total') {
    $estado = '';
    switch($tipo) {
        case 'pendientes':
            $estado = '1';
            break;
        case 'aprobadas':
            $estado = '2';
            break;
        case 'supervisor':
            $estado = '4';
            break;
        case 'eliminadas':
            $estado = '3';
            break;
    }
    
    if ($estado !== '') {
        $query .= " AND stat = ?";
        $params[] = $estado;
    }
}

// Para adjuntos, filtrar registros que tengan al menos un adjunto
if ($tipo === 'adjuntos') {
    $query .= " AND (
        (pn_recibo_servicios IS NOT NULL AND pn_recibo_servicios != '') OR
        (pn_ficha IS NOT NULL AND pn_ficha != '') OR
        (pn_carta_ex IS NOT NULL AND pn_carta_ex != '') OR
        (pn_contrato IS NOT NULL AND pn_contrato != '') OR
        (pn_cc IS NOT NULL AND pn_cc != '') OR
        (pni_cedula IS NOT NULL AND pni_cedula != '') OR
        (pni_aviso_op IS NOT NULL AND pni_aviso_op != '') OR
        (pni_servicios IS NOT NULL AND pni_servicios != '') OR
        (pni_referencia IS NOT NULL AND pni_referencia != '') OR
        (pni_cc IS NOT NULL AND pni_cc != '') OR
        (pni_carta_ex IS NOT NULL AND pni_carta_ex != '') OR
        (pj_registro_publico IS NOT NULL AND pj_registro_publico != '') OR
        (pj_aviso_ope IS NOT NULL AND pj_aviso_ope != '') OR
        (pj_cedula_pass IS NOT NULL AND pj_cedula_pass != '') OR
        (pj_servicios IS NOT NULL AND pj_servicios != '') OR
        (pj_cc IS NOT NULL AND pj_cc != '') OR
        (pj_carta_exo IS NOT NULL AND pj_carta_exo != '') OR
        (pj_contrato IS NOT NULL AND pj_contrato != '') OR
        (pni_contrato IS NOT NULL AND pni_contrato != '')
    )";
}

$query .= " ORDER BY date_time DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($resultados);
?>
