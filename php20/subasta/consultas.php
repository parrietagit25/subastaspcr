<?php 

try {
    $pdo = new PDO('mysql:host=db;dbname=db;charset=utf8mb4', 'parrieta', 'Dollar2022');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

$ultimo_id = $pdo -> query("SELECT * FROM cc_subastas WHERE id ='".$_GET['id']."'");
$rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET['aprobar'])) {


    foreach ($rows as $row) {

        echo ' Desea aprobar a <b>'.$row['nombre_completo'].'</b> Se le enviara un correo a con el codigo de aprobacion.';

    }

    echo '<input type="hidden" value="'.$_GET['id'].'" name="id_aprobar">';

}

if (isset($_GET['adjuntos'])) {

    foreach ($rows as $row) {

        if ($row['tipo_persona'] == 'NATURAL') {

            echo '<a href="'.$row['pn_recibo_servicios'].'" target="_blank">Recibo / Servicio</a><br>';

            echo '<a href="'.$row['pn_ficha'].'" target="_blank">Ficha</a><br>';

            echo '<a href="'.$row['pn_carta_ex'].'" target="_blank">Carta</a><br>';

            echo '<a href="'.$row['pn_cc'].'" target="_blank">Conosca a su cliente</a><br>';

        }

        if ($row['tipo_persona'] == 'NATURAL INDEPENDIENTE') {

            echo '<a href="'.$row['pni_cedula'].'" target="_blank">Ceduka / Pasaporte</a><br>';

            echo '<a href="'.$row['pni_aviso_op'].'" target="_blank">Aviso de Operaciones</a><br>';

            echo '<a href="'.$row['pni_servicios'].'" target="_blank">Servicios</a><br>';

            echo '<a href="'.$row['pni_referencia'].'" target="_blank">Referencia</a><br>';

            echo '<a href="'.$row['pni_cc'].'" target="_blank">Conosca a su cliente</a><br>';

            echo '<a href="'.$row['pni_carta_ex'].'" target="_blank">Carta</a><br>';

        }

        if ($row['tipo_persona'] == 'JURIDICA') {

            echo '<a href="'.$row['pj_registro_publico'].'" target="_blank">Registro Publico</a><br>';

            echo '<a href="'.$row['pj_aviso_ope'].'" target="_blank">Aviso de Operaciones</a><br>';

            echo '<a href="'.$row['pj_cedula_pass'].'" target="_blank">Cedula / Pasaporte</a><br>';

            echo '<a href="'.$row['pj_servicios'].'" target="_blank">Servicios</a><br>';

            echo '<a href="'.$row['pj_cc'].'" target="_blank">Conosca a su cliente</a><br>';

            echo '<a href="'.$row['pj_carta_exo'].'" target="_blank">Carta</a><br>';

        }

    }
    
}

if (isset($_GET['eliminar'])) {


    foreach ($rows as $row) {

        echo '<p style="color:red;"> Desea Eliminar el regsitros de <b>'.$row['nombre_completo'].'<p>.';

    }

    echo '<input type="hidden" value="'.$_GET['id'].'" name="id_eliminar">';

}

?>