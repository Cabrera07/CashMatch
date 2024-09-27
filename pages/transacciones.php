<!--------------------------------------------------- INCLUDES & DATABASE CONECTIONS --------------------------------------------------->
<?php require "../includes/header.php" ?>
<?php require "../includes/navbar.php" ?>
<?php require "../php/bd2.php" ?>

<!----------------------------------------------------------- FORMULARIO - OTRAS TRANSACCIONES ----------------------------------------------------------->
<section class="container-fluid px-md-5 py-5 pr-5 pl-5">
    <form method="post">
        <div class="card custom-card">
            <!--------------------------------------------------- TÍTULO DEL CARD--------------------------------------------------->
            <div class="card-header custom-header">Otras Transacciones - Depósitos, Ajustes y Notas</div>
            <div class="card-body">
                <div class="row">
                    <!-------------------------------------------------------------- PRIMERA COLUMNA ----------------------------------------------------------------------->
                    <div class="col-md-6">
                        <div class="card custom-subcard">
                            <div class="card-body">
                                <div class="mb-3" data-bs-theme="dark">
                                    <label for="inputFecha" class="form-label label-act">Fecha</label>
                                    <input type="date" class="form-control form-act" id="inputFecha" name="fecha">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-------------------------------------------------------------- SEGUNDA COLUMNA ----------------------------------------------------------------------->
                    <div class="col-md-6">
                        <div class="card custom-subcard">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="transacciones" class="form-label label-act">Transacciones</label>
                                    <select id="transacciones" class="form-select custom-select" name="Otransacciones">
                                        <?php
                                        $buscar_pro = mysqli_query($est, "SELECT * FROM proveedores ORDER BY nombre");

                                        // Arreglo de etiquetas de grupo
                                        $grupos = array(
                                            "Libro" => array("codigo_min" => 1, "codigo_max" => 5),
                                            "Banco" => array("codigo_min" => 6, "codigo_max" => 7),
                                            "Transferencias" => array("codigo_min" => 8, "codigo_max" => 9)
                                        );

                                        // Iterar sobre los grupos
                                        foreach ($grupos as $label => $rango) {
                                            echo "<optgroup label='$label'>";

                                            // Consultar transacciones según el rango de códigos
                                            $buscar_trans = mysqli_query($est, "SELECT * FROM transacciones WHERE codigo BETWEEN {$rango['codigo_min']} AND {$rango['codigo_max']} ORDER BY codigo ASC");

                                            // Iterar sobre las transacciones
                                            while ($transaccion = mysqli_fetch_assoc($buscar_trans)) {
                                                echo "<option value='" . $transaccion['codigo'] . "'>" . $transaccion['detalle'] . "</option>";
                                            }

                                            echo "</optgroup>";
                                        }
                                        $seleccionado = "";
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="inputMonto" class="form-label label-act">Monto</label>
                                    <input type="text" class="form-control form-act" id="inputMonto" name="monto" autocomplete="off" onkeypress="soloDecimales(event)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--------------------------------------------------------- CARD FOOTER - BOTONES --------------------------------------------------------->
            <div class="card-footer text-center mt-3 custom-footer">
                <button type="submit" class="btn btn-warning btn-footer mx-3" name="grabar2">Grabar</button>
                <button type="reset" class="btn btn-warning btn-footer mx-3">Nuevo</button>
            </div>
        </div>
    </form>
</section>

<!-------------------------------------------------------------------------- INCLUDE - FOOTER -------------------------------------------------------------------------->
<?php require "../includes/footer.php" ?>

<!--BOOTSTRAP JS-->
<script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous">
</script>

<!-------------------------------------------------------------------------- LOGICA - PHP -------------------------------------------------------------------------->
<?php
if (isset($_POST['grabar2'])) {
    // Verificar si se proporcionaron los datos requeridos
    if (isset($_POST['Otransacciones'], $_POST['fecha'], $_POST['monto']) && !empty($_POST['Otransacciones']) && !empty($_POST['fecha']) && !empty($_POST['monto'])) {
        // Sanitizar los datos de entrada
        $transaccion = mysqli_real_escape_string($est, $_POST['Otransacciones']);
        $fecha = mysqli_real_escape_string($est, $_POST['fecha']);
        $monto = mysqli_real_escape_string($est, $_POST['monto']);

        // Insertar los datos en la tabla "otros"
        $consulta = "INSERT INTO otros (transaccion, fecha, monto) VALUES ('$transaccion', '$fecha', '$monto')";
        $resultado = mysqli_query($est, $consulta);

        if ($resultado) {
            // Mostrar un mensaje de éxito si la inserción se realizó correctamente
            echo "<script>alert('Transacción registrada correctamente');</script>";
        } else {
            // Mostrar un mensaje de error si hubo un problema al ejecutar la consulta SQL
            echo "<script>alert('Error al registrar la transacción');</script>";
        }
    } else {
        // Mostrar un mensaje de error si algún dato requerido está ausente
        echo "<script>alert('Por favor, completa todos los campos');</script>";
    }
}
?>