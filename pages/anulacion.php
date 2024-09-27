<!--------------------------------------------------- INCLUDES & DATABASE CONECTIONS --------------------------------------------------->
<?php require "../includes/header.php" ?>
<?php require "../includes/navbar.php" ?>
<?php require "../php/bd2.php" ?>

<!-------------------------------------------------------------------------- LOGICA - PHP -------------------------------------------------------------------------->
<?php
// Verificar si se ha enviado el formulario de búsqueda
if (isset($_POST["buscar"])) {
    // Verificar si se ha enviado el número de cheque
    if (isset($_POST["numero_cheque"])) {
        // Obtener el número de cheque del formulario
        $numero_cheque = $_POST["numero_cheque"];

        // Realizar la primera validación: verificar si el cheque ya está anulado
        $consulta_anulado = "SELECT * FROM cheques WHERE numero_cheque='$numero_cheque' AND fecha_anulado IS NOT NULL AND detalle_anulado IS NOT NULL";
        $resultado_anulado = mysqli_query($est, $consulta_anulado);

        if (mysqli_num_rows($resultado_anulado) > 0) {
            // Mostrar un mensaje de error si el cheque ya está anulado
            echo "<script>alert('El cheque ya está anulado');</script>";
        } else {
            // Realizar la segunda validación: verificar si el cheque está fuera de circulación
            $consulta_circulacion = "SELECT * FROM cheques WHERE numero_cheque='$numero_cheque' AND fecha_circulacion < CURDATE()";
            $resultado_circulacion = mysqli_query($est, $consulta_circulacion);

            if (mysqli_num_rows($resultado_circulacion) > 0) {
                // Mostrar una alerta si el cheque está fuera de circulación
                echo "<script>alert('El cheque está fuera de circulación');</script>";
            } else {
                // Preparar la consulta para buscar el cheque por su número
                $query = "SELECT * FROM cheques WHERE numero_cheque = ?";
                $statement = $est->prepare($query);
                $statement->bind_param("s", $numero_cheque);

                // Ejecutar la consulta
                if ($statement->execute()) {
                    // Obtener el resultado de la consulta
                    $result = $statement->get_result();

                    // Verificar si se encontró algún resultado
                    if ($result->num_rows > 0) {
                        // Obtener los datos del cheque encontrado
                        $ckRow = $result->fetch_assoc();
                        $fecha = $ckRow["fecha"];
                        $beneficiario = $ckRow["beneficiario"];
                        $monto = $ckRow["monto"];
                        $descripcion = $ckRow["descripcion"];
                    } else {
                        // No se encontró ningún cheque con el número proporcionado
                        echo "<script>alert('No se encontró ningún cheque con el número proporcionado.');</script>";
                    }
                } else {
                    // Error al ejecutar la consulta
                    echo "<script>alert('Error al ejecutar la consulta: " . $statement->error . "');</script>";
                }
                // Cerrar el statement
                $statement->close();
            }
        }
    } else {
        // El número de cheque no fue proporcionado en el formulario
        echo "<script>alert('Por favor, proporcione el número de cheque.');</script>";
    }
}
?>

<!----------------------------------------------------------- FORMULARIO - ANULACION ----------------------------------------------------------->
<section class="container-fluid px-md-4 py-3 pr-4 pl-4">
    <form method="post">
        <div class="card custom-card">
            <!--------------------------------------------------- TÍTULO DEL CARD--------------------------------------------------->
            <div class="card-header custom-header">Anulación de Cheques</div>
            <div class="card-body">
                <div class="row">
                    <!-------------------------------------------------------------- PRIMERA COLUMNA ----------------------------------------------------------------------->
                    <div class="col-md-6">
                        <div class="card custom-subcard">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="inputNoCheque" class="form-label label-act">No. de Cheque</label>
                                        <input type="text" class="form-control form-act" autocomplete="off" id="inputNoCheque" name="numero_cheque" value="<?php echo isset($_POST['numero_cheque']) ? htmlspecialchars($_POST['numero_cheque']) : ''; ?>" oninput="validarNoCheque()">
                                    </div>
                                    <div class="col-md-6 mb-3 d-grid  d-md-flex justify-content-md-end">
                                        <button type="submit" id="buscarCheque" class="btn btn-warning btn-buscar mx-3" name="buscar">Buscar</button>
                                    </div>
                                </div>
                                <?php
                                // Verificar si se encontró algún resultado
                                if (isset($fecha)) {
                                    // Imprimir los datos del cheque
                                    echo "<div class='mb-3'>";
                                    echo "<label for='inputFecha' class='form-label label-act'>Fecha</label>";
                                    echo "<input type='date' class='form-control form-act' id='inputFecha' name='fecha' value='" . $fecha . "' readonly>";
                                    echo "</div>";

                                    echo "<div class='mb-3'>";
                                    echo "<label for='inputPago' class='form-label label-act'>Paguese a la Orden de</label>";
                                    echo "<select type='text' class='custom-select form-control' id='inputPago' name='beneficiario' readonly>"; // El select se deshabilita para que no se pueda modificar
                                    $buscar_pro = mysqli_query($est, "SELECT * FROM proveedores ORDER BY nombre");
                                    while ($pro_o = mysqli_fetch_assoc($buscar_pro)) {
                                        $seleccionado = ($pro_o['codigo'] == $beneficiario) ? "selected" : "";
                                        echo "<option value='" . $pro_o['codigo'] . "' " . $seleccionado . ">" . $pro_o['nombre'] . "</option>";
                                    }
                                    echo "</select>";
                                    echo "</div>";

                                    echo "<div class='mb-3'>";
                                    echo "<label for='inputSuma' class='form-label label-act'>La suma de</label>";
                                    echo "<input type='text' class='form-control form-act mb-2' id='inputSuma' name='monto' value='" . $monto . "' readonly>";
                                    echo "</div>";

                                    echo "<div class='mb-3'>";
                                    echo "<label for='inputDGastos' class='form-label label-act'>Descripción de Gastos</label>";
                                    echo "<input type='text' class='form-control form-act' id='inputDGastos' name='descripcion' value='" . $descripcion . "' readonly>";
                                    echo "</div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-------------------------------------------------------------- SEGUNDA COLUMNA ----------------------------------------------------------------------->
                    <div class="col-md-6">
                        <div class="card custom-subcard">
                            <div class="card-body">
                                <div class="mb-3" data-bs-theme="dark">
                                    <label for="inputfAnulacion" class="form-label label-act">Fecha de Anulación</label>
                                    <input type="date" class="form-control form-act" id="inputfAnulacion" name="fecha_anulado">
                                </div>
                                <div class="mb-0">
                                    <label for="inputdAnul" class="form-label label-act">Detalle de Anulación</label>
                                    <textarea class="form-control form-act" id="inputdAnul" name="detalle_anulado" oninput="validarDetalledeAnulacion()" rows="4" autocomplete="off"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--------------------------------------------------------- CARD FOOTER - BOTONES --------------------------------------------------------->
            <div class="card-footer text-center custom-footer">
                <button type="submit" class="btn btn-warning btn-footer mx-3" name="anular">Anular</button>
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
if (isset($_POST['anular'])) {
    // Obtener la fecha de anulación del formulario
    $fecha_anulado = $_POST["fecha_anulado"];
    // Obtener la fecha del cheque del formulario
    $fecha = $_POST["fecha"];

    // Convertir las fechas a objetos DateTime
    $fecha_anulado_obj = new DateTime($fecha_anulado);
    $fecha_obj = new DateTime($fecha);

    // Verificar si la fecha de anulación es anterior o igual a la fecha del cheque
    if ($fecha_anulado_obj <= $fecha_obj) {
        // Mostrar un mensaje de error si la fecha de anulación es inválida
        echo "<script>alert('La fecha de anulación debe ser posterior a la fecha del cheque');</script>";
    } else {
        // Verificar si el número de cheque está presente en el formulario
        if (isset($_POST['numero_cheque']) && !empty($_POST['numero_cheque'])) {
            // Sanitizar el número de cheque
            $numero_cheque = trim($_POST['numero_cheque']);

            // Realizar la primera validación: verificar si el cheque ya está anulado
            $consulta_anulado = "SELECT * FROM cheques WHERE numero_cheque='$numero_cheque' AND fecha_anulado IS NOT NULL AND detalle_anulado IS NOT NULL";
            $resultado_anulado = mysqli_query($est, $consulta_anulado);

            if (mysqli_num_rows($resultado_anulado) > 0) {
                // Mostrar un mensaje de error si el cheque ya está anulado
                echo "<script>alert('El cheque ya está anulado');</script>";
            } else {
                // Realizar la segunda validación: verificar si el cheque está fuera de circulación
                $consulta_circulacion = "SELECT * FROM cheques WHERE numero_cheque='$numero_cheque' AND fecha_circulacion < CURDATE()";
                $resultado_circulacion = mysqli_query($est, $consulta_circulacion);

                if (mysqli_num_rows($resultado_circulacion) > 0) {
                    // Mostrar una alerta si el cheque está fuera de circulación
                    echo "<script>alert('El cheque está fuera de circulación');</script>";
                } else {
                    // Si pasa ambas validaciones, proceder con la anulación del cheque
                    // Verificar si la fecha de anulación y el detalle de anulación están presentes
                    if (isset($_POST['fecha_anulado'], $_POST['detalle_anulado']) && !empty($_POST['fecha_anulado']) && !empty($_POST['detalle_anulado'])) {
                        // Sanitizar la fecha de anulación y el detalle de anulación
                        $fecha_anulado = trim($_POST['fecha_anulado']);
                        $detalle_anulado = trim($_POST['detalle_anulado']);

                        // Actualizar la tabla de cheques con los valores proporcionados
                        $consulta = "UPDATE cheques SET fecha_anulado='$fecha_anulado', detalle_anulado='$detalle_anulado' WHERE numero_cheque='$numero_cheque'";
                        $resultado = mysqli_query($est, $consulta);

                        if ($resultado) {
                            // Mostrar un mensaje de éxito si la actualización se realizó correctamente
                            echo "<script>alert('Cheque anulado correctamente');</script>";
                        } else {
                            // Mostrar un mensaje de error si hubo un problema al ejecutar la consulta SQL
                            echo "<script>alert('Error al anular el cheque');</script>";
                        }
                    } else {
                        // Mostrar un mensaje de error si la fecha de anulación o el detalle de anulación están ausentes
                        echo "<script>alert('Por favor, completa la fecha y el detalle de anulación');</script>";
                    }
                }
            }
        } else {
            // Mostrar un mensaje de error si el número de cheque está ausente
            echo "<script>alert('Por favor, proporciona el número de cheque');</script>";
        }
    }
}
?>