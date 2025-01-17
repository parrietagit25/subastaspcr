<?php 

try {
    $pdo = new PDO('mysql:host=db;dbname=subastas;charset=utf8mb4', 'root', 'rootpass');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

$ultimo_id = $pdo -> query("SELECT * FROM cc_subastas WHERE id ='".$_GET['id']."'");
$rows = $ultimo_id->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET['aprobar'])) {


    foreach ($rows as $row) {

        echo ' Desea aprobar a <b>'.$row['nombre_completo'].'</b> Se le enviara un correo a con el codigo de aprobacion.<br>
                <h3 style="color:red;">ANTES DE APROBAR, VERIFIQUE LOS DOCUMENTOS ADJUNTOS</h3>
                <b style="color:red;">Verifique si todos los documentos adjuntos se pueden visualizar. Si no es el caso, 
                por favor contacte al cliente y solicítele que vuelva a subir los documentos solicitados.</b>
        ';

    }

    echo '<input type="hidden" value="'.$_GET['id'].'" name="id_aprobar">';

}

if (isset($_GET['enviar_email'])) {

    foreach ($rows as $row) {

        echo ' Desea Enviar un correo a <b>'.$row['nombre_completo'].'</b>.<br>
             <textarea name="mensaje_send_email" id="" cols="30" rows="10" class="form-control"></textarea>
        ';

    }

    echo '<input type="hidden" value="'.$_GET['id'].'" name="id_aprobar_send_email">';
    echo '<input type="hidden" value="'.$row['email'].'" name="email_send_email">';

}

if (isset($_GET['adjuntos'])) {

    foreach ($rows as $row) {

        if ($row['tipo_persona'] == 'NATURAL') {

            if (!empty($row['pn_recibo_servicios'])) {
                echo '<a href="'.$row['pn_recibo_servicios'].'" target="_blank">Recibo / Servicio</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo recibo no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pn_ficha'])) {
                echo '<a href="'.$row['pn_ficha'].'" target="_blank">Ficha</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo ficha no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            // echo '<a href="'.$row['pn_carta_ex'].'" target="_blank">Carta</a><br>';

            if (!empty($row['pn_cc'])) {
                echo '<a href="'.$row['pn_cc'].'" target="_blank">conozca a su cliente</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo conozca a su cliente no se ha subido  correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pn_cedula'])) {
                echo '<a href="'.$row['pn_cedula'].'" target="_blank">Cedula o Pasaporte</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo Cedula o Pasaporte no se ha subido  correctamente, por favor contactar con el cliente </span> <br>';
            }
        }

        if ($row['tipo_persona'] == 'NATURAL INDEPENDIENTE') {

            if (!empty($row['pni_cedula'])) {
                echo '<a href="'.$row['pni_cedula'].'" target="_blank">Cédula / Pasaporte</a><br>';
            }else {
                echo '<span style="color:red;"> El Archivo cedula no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pni_aviso_op'])) {
                echo '<a href="'.$row['pni_aviso_op'].'" target="_blank">Aviso de Operaciones</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo aviso de operaciones no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pni_servicios'])) {
                echo '<a href="'.$row['pni_servicios'].'" target="_blank">Servicios</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo de servicios no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pni_referencia'])) {
                echo '<a href="'.$row['pni_referencia'].'" target="_blank">Referencia</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo Referencia no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pni_cc'])) {
                echo '<a href="'.$row['pni_cc'].'" target="_blank">conozca a su cliente</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo conozca a su cliente no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            //echo '<a href="'.$row['pni_carta_ex'].'" target="_blank">Carta</a><br>';

        }

        if ($row['tipo_persona'] == 'JURIDICA') {

            if (!empty($row['pj_registro_publico'])) {
                echo '<a href="'.$row['pj_registro_publico'].'" target="_blank">Registro Público</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo Registro Público no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pj_aviso_ope'])) {
                echo '<a href="'.$row['pj_aviso_ope'].'" target="_blank">Aviso de Operaciones</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo aviso de operaciones no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pj_cedula_pass'])) {
                echo '<a href="'.$row['pj_cedula_pass'].'" target="_blank">Cédula / Pasaporte</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo Cédula / Pasaporte no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pj_servicios'])) {
                echo '<a href="'.$row['pj_servicios'].'" target="_blank">Servicios</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo Servicios no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            if (!empty($row['pj_cc'])) {
                echo '<a href="'.$row['pj_cc'].'" target="_blank">conozca a su cliente</a><br>';
            }else {
                echo '<span style="color:red;"> El archivo conozca a su cliente no se ha subido correctamente, por favor contactar con el cliente </span> <br>';
            }

            //echo '<a href="'.$row['pj_carta_exo'].'" target="_blank">Carta</a><br>';

        }

    }

}


if (isset($_GET['eliminar'])) {


    foreach ($rows as $row) {

        echo '<p style="color:red;"> Desea Eliminar el regsitros de <b>'.$row['nombre_completo'].'<p>.';

    }

    echo '<input type="hidden" value="'.$_GET['id'].'" name="id_eliminar">';

}

if (isset($_GET['reenviar_codigo'])) {  ?>

    <h2>Quieres reenviar el codigo?</h2>
    <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id_reenviar">   
  
<?php } 

if (isset($_GET['edit_reg'])) {

    foreach ($rows as $row) { ?>

        <label for="">Tipo de Persona</label>
        <input type="text" value="<?php echo $row['tipo_persona']; ?>" class="form-control" name="edit_tipo_persona">
        <label for="">Nombre completo</label>
        <input type="text" value="<?php echo $row['nombre_completo']; ?>" class="form-control" name="edit_nombre_completo">
        <label for="">Email</label>
        <input type="text" value="<?php echo $row['email']; ?>" class="form-control" name="edit_email">
        <label for="">Telefono</label>
        <input type="text" value="<?php echo $row['telefono']; ?>" class="form-control" name="edit_telefono">
        <input type="hidden" value="<?php echo $_GET['id']; ?>" name="id_user_edit">

   <?php  }
    
}

?>