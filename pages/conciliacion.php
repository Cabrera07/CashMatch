<!--------------------------------------------------- INCLUDES & DATABASE CONECTIONS --------------------------------------------------->
<?php require "../includes/header.php" ?>
<?php require "../includes/navbar.php" ?>
<?php require "../php/bd2.php" ?>

<!-------------------------------------------------------------------------- LOGICA - PHP -------------------------------------------------------------------------->
<?php

// INICIALIZANDO VARIABLES 
$dia = "";
$mesCon = "";
$agno = "";
$dia_anterior = "";
$mes_anterior = "";
$agno_anterior = "";

$saldo_anterior = "";

$masdepositos = "";
$maschequesanulados = "";
$masnotascredito = "";
$masajusteslibro = "";

$sub1 = "";
$subtotal1 = "";

$menoschequesgirados = "";
$menosnotasdebito = "";
$menosajusteslibro = "";
$sub2 = "";
$saldolibros = "";
$saldobanco = "";
$masdepositostransito = "";
$menoschequescirculacion = "";
$masajustesbanco = "";
$sub3 = "";
$saldo_conciliado = "";

$lable_saldo_anterior = " ";
$etiqueta_conciliado = "";

$saldo_final ="";
$subtotal_final = "";

$saldo_banco_input = "";

// FUNCION PARA OBTENER LOS MESES DE LA BD
function obtenerMeses($est)
{
    $meses = array();
    $sql = "SELECT mes, nombre_mes FROM meses";
    $resultado = $est->query($sql);
    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $meses[] = $fila;
        }
        $resultado->free();
    } else {
        echo "Error al obtener los meses: " . $est->error;
    }
    return $meses;
}

// FUNCION PARA OBTENER LOS ULTIMOS 5 AÑOS 
function obtenerYear()
{
    $yearActual = date("Y");
    $years = array();
    for ($i = 0; $i < 5; $i++) {
        $years[] = $yearActual - $i;
    }
    return $years;
}


if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear']) && isset($_POST['inputSaldoB'])) {
    // Obtener mes y año seleccionados
    $mesSeleccionado = $_POST['inputMes'];
    $yearSeleccionado = $_POST['inputYear'];
    
    if (empty($_POST['inputSaldoB'])) {
        echo "<script>alert('Por favor, ingrese el saldo en el campo correspondiente.');</script>";
        $saldo_banco_input = 0;
    } else {
        $saldo_banco_input = $_POST['inputSaldoB'];
        $dia_actual = date("d");
        $etiqueta_conciliado = "$dia_actual DE $mesSeleccionado DE $yearSeleccionado";

    }
        $sql = "SELECT COUNT(*) AS count FROM conciliacion WHERE mes = $mesSeleccionado AND agno = $yearSeleccionado";
        $resultado = $est->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        if ($fila['count'] > 0) {
            echo "<script>alert('Este mes ya ha sido conciliado. Por favor, continúe con el siguiente.');</script>";
        } else {
            // Verificar si el mes anterior al seleccionado está conciliado
            $mesAnterior = $mesSeleccionado - 1;
            if ($mesAnterior == 0) {
                $mesAnterior = 12;
                $yearAnterior = $yearSeleccionado - 1;
            } else {
                $yearAnterior = $yearSeleccionado;
            }

            $sql = "SELECT COUNT(*) AS count FROM conciliacion WHERE mes = $mesAnterior AND agno = $yearAnterior";
            $resultado = $est->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
                $fila = $resultado->fetch_assoc();
                if ($fila['count'] == 0) {
                    // El mes anterior al seleccionado no está conciliado, mostrar alerta
                    echo "<script>alert('El mes anterior al seleccionado no está conciliado. No se puede realizar la conciliación.');</script>";
                } else {
                    // Preparar la consulta para buscar la conciliación por mes y año
                    $query = "SELECT * FROM conciliacion WHERE mes = ? AND agno = ?";
                    $statement = $est->prepare($query);
                    $statement->bind_param("ii", $mesAnterior, $yearAnterior); // ii indica dos valores enteros

                    // Ejecutar la consulta
                    if ($statement->execute()) {
                        // Obtener el resultado de la consulta
                        $result = $statement->get_result();

                        // Verificar si se encontró algún resultado
                        if ($result->num_rows > 0) {

                            
                            // Obtener los datos de la conciliación encontrada
                            $conciliacion = $result->fetch_assoc();

                            // Obtener cada campo de la tabla de conciliación
                            $dia = $conciliacion["dia"];
                            $mesCon = $conciliacion["mes"];
                            $agno = $conciliacion["agno"];

                            $lable_saldo_anterior = " " . $dia . ' DE ' . $mesCon . ' DE ' . $agno;;

                            $dia_anterior = $conciliacion["dia_anterior"];
                            $mes_anterior = $conciliacion["mes_anterior"];
                            $agno_anterior = $conciliacion["agno_anterior"];

                            $saldo_anterior = $conciliacion["saldo_anterior"];

                            $masdepositos = $conciliacion["masdepositos"];
                            $maschequesanulados = $conciliacion["maschequesanulados"];
                            $masnotascredito = $conciliacion["masnotascredito"];
                            $masajusteslibro = $conciliacion["masajusteslibro"];

                            $sub1 = $conciliacion["sub1"];
                            $subtotal1 = $conciliacion["subtotal1"];

                            $menoschequesgirados = $conciliacion["menoschequesgirados"];
                            $menosnotasdebito = $conciliacion["menosnotasdebito"];
                            $menosajusteslibro = $conciliacion["menosajusteslibro"];

                            $sub2 = $conciliacion["sub2"];
                            $saldolibros = $conciliacion["saldolibros"];

                            $saldobanco = $conciliacion["saldobanco"];

                            $masdepositostransito = $conciliacion["masdepositostransito"];
                            $menoschequescirculacion = $conciliacion["menoschequescirculacion"];
                            $masajustesbanco = $conciliacion["masajustesbanco"];
                            $sub3 = $conciliacion["sub3"];
                            $saldo_conciliado = $conciliacion["saldo_conciliado"];

                            $subtotal_final = - ($masdepositostransito + $menoschequescirculacion + $masajustesbanco);

                            $saldo_final = $saldo_banco_input + $subtotal_final;

                        } else {
                            // No se encontró ninguna conciliación para el mes y año seleccionados
                            echo "<script>alert('No se encontró ninguna conciliación para el mes y año seleccionados.');</script>";
                        }
                    } else {
                        // Error al ejecutar la consulta
                        echo "<script>alert('Error al ejecutar la consulta: " . $statement->error . "');</script>";
                    }
                    // Cerrar el statement
                    $statement->close();
                }
            }
        }
    }
} 


?>

<!----------------------------------------------------------------------  ALERTAS ------------------------------------------------------------------->
<!------- ALERTA - LASUAMADE ------->
<div class="alert alert-warning alert-dismissible fade show mt-3 mb-2 mx-4" id="alertaDecimales" style="display: none;">
	<i class="bi bi-exclamation-triangle-fill"></i>
	<strong>Warning:</strong>
	<span id="mensajeAlerta"></span>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<!----------------------------------------------------------- FORMULARIO - CONCILIACION ----------------------------------------------------------->
<section class="container-fluid px-md-4 py-3 pr-4 pl-4">
    <form method="post" id="conciliacionForm">
        <div class="card custom-card">
            <!--------------------------------------------------- TÍTULO DEL CARD--------------------------------------------------->
            <div class="card-header custom-header">Conciliación Bancaria</div>
            <div class="card-body">
                <div class="row">
                    <!--------------------------------------------------- PRIMERA COLUMNA - LABELS --------------------------------------------------->
                    <div class="col-md-4">
                        <div class="card custom-subcard">
                            <div class="card-body">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="inputMes" class="form-label label-con">Mes</label>
                                        <select class="custom-select form-control" name="inputMes" id="inputMes">
                                           <!-- SELECT DE LA BD CONCILIACION - 'meses' -->
                                           <?php
                                            $meses = obtenerMeses($est);
                                            if (!empty($meses)) {
                                                foreach ($meses as $mes) {
                                                    // Verificar si el mes coincide con el seleccionado previamente
                                                    $selected = ($_POST['inputMes'] ?? '') == $mes['mes'] ? 'selected' : '';
                                                    echo '<option value="' . $mes['mes'] . '" ' . $selected . '>' . $mes['nombre_mes'] . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No se encontraron meses.</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <hr>
                                </div>
                                <div class=" mt-2 mb-4">
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?>
                                        <label class="form-label label-con-h1 text-uppercase" id="saldo-anterior-label" for="inputSaldo">SALDO SEGÚN LIBRO AL 
                                            <?php echo $lable_saldo_anterior; ?></label>
                                    <?php } else { ?>
                                        <label class="form-label label-con-h1 text-uppercase" id="saldo-anterior-label" for="inputSaldo">SALDO SEGÚN LIBRO AL</label>
                                    <?php } ?>
                                </div>
                                <div class="px-md-2 mt-2 mb-4">
                                    <label class="form-label label-con" for="inputDepo">Más: Déposito</label>
                                </div>
                                <div class="px-md-4 mt-2 mb-4">
                                    <label class="form-label label-con" for="inputAnulCks">Cheques Anulados</label>
                                </div>
                                <div class="px-md-4 mt-2 mb-4">
                                    <label class="form-label label-con" for="inputNcredit">Notas de Crédito</label>
                                </div>
                                <div class="px-md-4 mt-2 mb-3">
                                    <label class="form-label label-con" for="inputCrAjustes">Ajustes</label>
                                </div>
                                <hr>
                                <div class="mt-4 mb-4">
                                    <label class="form-label label-con-h1" style="margin-top: 1.4cm;" for="inputSubtotal">SUBTOTAL</label>
                                </div>
                                <hr>
                                <div class="px-md-2 mt-4 mb-4">
                                    <label class="form-label label-con" for="inputGCks">Menos: Cheques girados en el mes</label>
                                </div>
                                <div class="px-md-4 mt-2 mb-4">
                                    <label class="form-label label-con" for="inputNdebit">Notas de Débitos</label>
                                </div>
                                <div class="px-md-4 mt-2 mb-3">
                                    <label class="form-label label-con" for="inputDbAjustes">Ajuste</label>
                                </div>
                                <hr style="margin-top: 0.5cm;">
                                <div class="mb-1">
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?>
                                        <label class="form-label label-con-h1" style="margin-top: 1.9cm;" for="inputSaldoCon">SALDO CONCILIADO SEGÚN LIBRO AL 
                                            <?php echo $etiqueta_conciliado; ?></label>
                                    <?php } else { ?>
                                        <label class="form-label label-con-h1" style="margin-top: 1.9cm;" for="inputSaldoCon">SALDO CONCILIADO SEGÚN LIBRO AL</label>
                                    <?php } ?>            
                                </div>
                                <hr style="margin-top: 0.9cm;">
                                <hr>
                                <div class="mt-3">
                                <?php if (isset($_POST['inputSaldoB'])) { ?>
                                    <label class="form-label label-con-h1" for="inputSaldoB">SALDO EN BANCO AL 
                                            <?php echo $etiqueta_conciliado; ?></label>
                                    <?php } else { ?>
                                        <label class="form-label label-con-h1" for="inputSaldoB">SALDO EN BANCO AL </label>
                                <?php } ?>   
                                </div>
                                <hr style="margin-top: 1cm;">
                                <div class="mt-2 mb-4">
                                    <label class="form-label label-con" for="inputDepTran">Más: Dépositos en Transito</label>
                                </div>
                                <div class="mt-2 mb-4">
                                    <label class="form-label label-con" for="inputCksCir">Menos: Cheques en Circulación</label>
                                </div>
                                <div class="mt-2 mb-4">
                                    <label class="form-label label-con" for="inputMAjustes">Más: Ajustes</label>
                                </div>
                                <hr>
                                <div class="mt-2 mb-4">
                                <?php if (isset($_POST['inputSaldoB'])) { ?>
                                    <label class="form-label label-con-h1" style="margin-top: 1.6cm;" for="inputConSaldo">SALDO CONCILIADO IGUAL A BANCO AL
                                            <?php echo $etiqueta_conciliado; ?></label>
                                    <?php } else { ?>
                                        <label class="form-label label-con-h1" style="margin-top: 1.6cm;" for="inputConSaldo">SALDO CONCILIADO IGUAL A BANCO AL</label>
                                <?php } ?>                                      
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--------------------------------------------------- SEGUNDA COLUMNA - LABELS Y INPUTS --------------------------------------------------->
                    <div class="col-md-4">
                        <div class="card custom-subcard">
                            <div class="card-body">
                                <div class="mb-3 mt-2">
                                    <label for="inputYear" class=" label-con label-con">Año</label>
                                    <select class="custom-select form-control" name="inputYear" id="inputYear">
                                        <!-- SELECT CON LA FUNCION 'obtenerYear'-->
                                        <?php
                                        // Llamar a la función para obtener los años
                                        $years = obtenerYear();
                                        if (!empty($years)) {
                                            foreach ($years as $year) {
                                                // Verificar si el año coincide con el seleccionado previamente
                                                $selected = ($_POST['inputYear'] ?? '') == $year ? 'selected' : '';
                                                echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">No se encontraron años.</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <hr>
                                <div class="mb-3 mt-5 pt-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputDepo" name="inputDepo" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $masdepositos; ?>" <?php } ?>>
                                </div>
                                <div class="mt-1 mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputAnulCks" name="inputAnulCks" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $maschequesanulados; ?>" <?php } ?>>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputNcredit" name="inputNcredit" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $masnotascredito; ?>" <?php } ?>>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputCrAjustes" name="inputCrAjustes" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $masajusteslibro; ?>" <?php } ?>>
                                </div>
                                <hr style="margin-top: 1.2cm;">
                                <div class="mt-3 mb-2">
                                    <label class="form-label label-con-subtotal text-center" for="inputSubCre">Subtotal</label>
                                </div>
                                <hr style="margin-top: 2.4cm;">
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputGCks" name="inputGCks" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $menoschequesgirados; ?>" <?php } ?>>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputNdebit" name="inputNdebit" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $menosnotasdebito; ?>" <?php } ?>>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputDbAjustes" name="inputDbAjustes" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $menosajusteslibro; ?>" <?php } ?>>
                                </div>
                                <hr style="margin-top: 0.8cm;">
                                <div class="mt-3 mb-3">
                                    <label class="form-label label-con-subtotal text-center" for="inputSubDb">Subtotal</label>
                                </div>
                                <hr style="margin-top: 3.5cm;">
                                <hr>
                                <hr style="margin-top: 2.5cm;">
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputDepTran" name="inputDepTran" readonly
                                    <?php if (isset($_POST['inputSaldoB'])) { ?> value="<?php echo $masdepositostransito; ?>" <?php } ?>>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputCksCir" name="inputCksCir" readonly
                                    <?php if (isset($_POST['inputSaldoB'])) { ?> value="<?php echo $menoschequescirculacion; ?>" <?php } ?>>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputMAjustes" name="inputMAjustes" readonly
                                    <?php if (isset($_POST['inputSaldoB'])) { ?> value="<?php echo $masajustesbanco; ?>" <?php } ?>>
                                </div>
                                <hr style="margin-top: 0.8cm;">
                                <div class="mt-3 mb-4">
                                    <label class="form-label label-con-subtotal text-center" for="inputSubCon">Subtotal</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--------------------------------------------------- TERCERA COLUMNA - INPUTS --------------------------------------------------->
                    <div class="col-md-4">
                        <div class="card custom-subcard">
                            <div class="card-body">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-warning btn-con btn-sm" id="realizarConciliacion" name="realizarConciliacion">Realizar Conciliación</button>
                                </div>
                                <hr>
                                <div class="mb-1">
                                    <input type="text" class="form-control form-con input-disabled" id="inputSaldo" name="inputSaldo" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $saldo_anterior; ?>" <?php } ?> >
                                </div>
                                <hr style="margin-top: 7.5cm;">
                                <div class="mb-3 ">
                                    <input type="text" class="form-control form-con input-disabled" id="inputSubCre" name="inputSubCre" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $sub1; ?>" <?php } ?>>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputSubtotal" name="inputSubtotal" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $subtotal1; ?>" <?php } ?>>
                                </div>
                                <hr style="margin-top: 0.8cm;">
                                <hr style="margin-top: 5.5cm;">
                                <div class="mb-5 mt-4">
                                    <input type="text" class="form-control form-con input-disabled" id="inputSubDb" name="inputSubDb" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $sub2; ?>" <?php } ?>>
                                </div>
                                <div class="mb-1 mt-2">
                                    <input type="text" class="form-control form-con input-disabled" id="inputSaldoCon" name="inputSaldoCon" readonly
                                    <?php if (isset($_POST['realizarConciliacion']) && isset($_POST['inputYear'])) { ?> value="<?php echo $saldolibros; ?>" <?php } ?>>
                                </div>
                                <hr style="margin-top: 0.5cm;">
                                <hr>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con" name = "inputSaldoB" id="inputSaldoB" onkeypress="soloDecimales(event)" 
                                    value="<?php echo $_POST['inputSaldoB'] ?? ''; ?>">
                                </div>
                                <hr style="margin-top: 0.9cm;">
                                <hr style="margin-top: 5.5cm;">
                                <div class="mb-4">
                                    <input type="text" class="form-control form-con input-disabled" id="inputSubCon" name="inputSubCon" readonly
                                    <?php if (isset($_POST['inputSaldoB'])) { ?> value="<?php echo $subtotal_final; ?>" <?php } ?>>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control form-con input-disabled" id="inputConSaldo" name="inputConSaldo" readonly
                                    <?php if (isset($_POST['inputSaldoB'])) { ?> value="<?php echo $saldo_final; ?>" <?php } ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--------------------------------------------------- CARD FOOTER - BOTONES --------------------------------------------------->
            <div class="card-footer text-center custom-footer">
                <button type="submit" class="btn btn-warning btn-footer mx-3" name="grabarConciliacion">Grabar</button>
                <button type="submit" class="btn btn-warning btn-footer mx-3" name="resetConciliacion" onclick="resetForm()">Nuevo</button>
            </div>
        </div>
    </form>
</section>

<!--------------------------------------------------- INCLUDE - FOOTER --------------------------------------------------->
<?php require "../includes/footer.php" ?>

<!--BOOTSTRAP JS-->
<script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
    crossorigin="anonymous">
</script>

<!-------------------------------------------------------------------------- LOGICA - PHP -------------------------------------------------------------------------->
<?php
if (isset($_POST['grabarConciliacion'])) {
    // Preparar los valores para la inserción
    $mes = $_POST['inputMes'];
    $agno = $_POST['inputYear'];
    $dia = date("d"); // Obtener el día actual

    $saldo_anterior = isset($_POST['inputSaldo']) ? $_POST['inputSaldo'] : '';
    $masdepositos = isset($_POST['inputDepo']) ? $_POST['inputDepo'] : '';
    $maschequesanulados = isset($_POST['inputAnulCks']) ? $_POST['inputAnulCks'] : '';
    $masnotascredito = isset($_POST['inputNcredit']) ? $_POST['inputNcredit'] : '';
    $masajusteslibro = isset($_POST['inputCrAjustes']) ? $_POST['inputCrAjustes'] : '';
    $sub1 = isset($_POST['inputSubCre']) ? $_POST['inputSubCre'] : '';
    $subtotal1 = isset($_POST['inputSubtotal']) ? $_POST['inputSubtotal'] : '';

    $menoschequesgirados = isset($_POST['inputGCks']) ? $_POST['inputGCks'] : '';
    $menosnotasdebito = isset($_POST['inputNdebit']) ? $_POST['inputNdebit'] : '';
    $menosajusteslibro = isset($_POST['inputDbAjustes']) ? $_POST['inputDbAjustes'] : '';

    $sub2 = isset($_POST['inputSubDb']) ? $_POST['inputSubDb'] : '';
    $saldolibros = isset($_POST['inputSaldoCon']) ? $_POST['inputSaldoCon'] : '';

    $saldobanco = $_POST['inputSaldoB'];

    $masdepositostransito = isset($_POST['inputDepTran']) ? $_POST['inputDepTran'] : '';
    $menoschequescirculacion = isset($_POST['inputCksCir']) ? $_POST['inputCksCir'] : '';
    $masajustesbanco = isset($_POST['inputMAjustes']) ? $_POST['inputMAjustes'] : '';

    $sub3 = isset($_POST['inputSubCon']) ? $_POST['inputSubCon'] : '';
    $saldo_conciliado = isset($_POST['inputConSaldo']) ? $_POST['inputConSaldo'] : '';

    // Obtener los valores para mes_anterior, dia_anterior y agno_anterior del último registro
    $sql_ultimo = "SELECT mes, dia, agno FROM conciliacion ORDER BY mes DESC LIMIT 1";
    $resultado_ultimo = $est->query($sql_ultimo);

    if ($resultado_ultimo && $resultado_ultimo->num_rows > 0) {
        $fila_ultimo = $resultado_ultimo->fetch_assoc();
        $mes_anterior = $fila_ultimo['mes'];
        $dia_anterior = $fila_ultimo['dia'];
        $agno_anterior = $fila_ultimo['agno'];
    } else {
        // Si no hay registros previos, puedes asignar valores predeterminados o vacíos
        $mes_anterior = '';
        $dia_anterior = '';
        $agno_anterior = '';
    }

    // Realizar la inserción
    $sql_insert = "INSERT INTO conciliacion (dia, mes, agno, mes_anterior, dia_anterior, agno_anterior, saldo_anterior, masdepositos, maschequesanulados, masnotascredito, masajusteslibro, sub1, subtotal1, menoschequesgirados, menosnotasdebito, menosajusteslibro, sub2, saldolibros, saldobanco, masdepositostransito, menoschequescirculacion, masajustesbanco, sub3, saldo_conciliado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $est->prepare($sql_insert);
    $stmt_insert->bind_param("iiiissssssssssssssssssss", $dia, $mes, $agno, $mes_anterior, $dia_anterior, $agno_anterior, $saldo_anterior, $masdepositos, $maschequesanulados, $masnotascredito, $masajusteslibro, $sub1, $subtotal1, $menoschequesgirados, $menosnotasdebito, $menosajusteslibro, $sub2, $saldolibros, $saldobanco, $masdepositostransito, $menoschequescirculacion, $masajustesbanco, $sub3, $saldo_conciliado);
    $resultado = $stmt_insert->execute();

    if ($resultado) {
        echo "<script>alert('Datos guardados correctamente');</script>";
    } else {
        echo "<script>alert('Error al guardar los datos');</script>";
    }

    // Cerrar la consulta preparada
    $stmt_insert->close();
}


?>