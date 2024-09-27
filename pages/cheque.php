<!--------------------------------------------------- INCLUDES & DATABASE CONECTIONS --------------------------------------------------->
<?php require "../includes/header.php" ?>
<?php require "../includes/navbar.php" ?>
<?php require "../php/bd2.php" ?>


<!----------------------------------------------------------------------  ALERTAS ------------------------------------------------------------------->

<!------- ALERTA - NoCHEQUE------->
<div class="alert alert-warning alert-dismissible fade show mt-3 mb-2 mx-4" id="alertaNoCheque" style="display: none;">
	<i class="bi bi-exclamation-triangle-fill"></i>
	<strong>Warning:</strong> Ese campo solo puede contener numeros.
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<!------- ALERTA - LASUAMADE ------->
<div class="alert alert-warning alert-dismissible fade show mt-3 mb-2 mx-4" id="alertaDecimales" style="display: none;">
	<i class="bi bi-exclamation-triangle-fill"></i>
	<strong>Warning:</strong>
	<span id="mensajeAlerta"></span>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<!------- ALERTA - DETALLE------->
<div class="alert alert-warning alert-dismissible fade show mt-3 mb-2 mx-4" id="alertaDetalle" style="display: none;">
	<i class="bi bi-exclamation-triangle-fill"></i>
	<strong>Warning:</strong>
	<span id="mensajeAlertaDetalle"></span>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!----------------------------------------------------------- FORMULARIO - CHEQUES ----------------------------------------------------------->
<section class="container-fluid px-md-4 py-3 pr-4 pl-4">
	<form method="post" id="myForm" onsubmit="return validarSuma()">
		<div class="card custom-card">
			<!--------------------------------------------------- TÍTULO DEL CARD--------------------------------------------------->
			<div class="card-header custom-header">Creación</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div class="card custom-subcard">
							<!--------------------------------------------------- SUBCARD TÍTULO --------------------------------------------------->
							<div class="card-header custom-subheader">Cheques</div>
							<div class="card-body">
								<div class="row">
									<!-------------------------------------------------------------- PRIMERA COLUMNA ----------------------------------------------------------------------->
									<div class="col-md-6 mb-3">
										<label for="inputNoCheque" class="form-label label-act">No. de Cheque</label>
										<input type="text" class="form-control form-act" name="numero_cheque" id="inputNoCheque" autocomplete="off" oninput="validarNoCheque()" maxlength="6" required>
										<span id="mensajeCheque"></span>
									</div>
									<div class="col-md-6 mb-3" data-bs-theme="dark">
										<label for="inputFecha" class="form-label label-act">Fecha</label>
										<input type="date" class="form-control form-act" name="fecha" id="inputFecha">
									</div>
								</div>
								<div class="mb-3">
									<label for="inputPago" class="form-label">Paguese a la Orden de</label>
									<select class="custom-select form-control" name="beneficiario" id="inputPago">
										<!-- SELECT DE LA BD CONCILIACION - 'proveedores' -->
										<?php
										$buscar_pro = mysqli_query($est, "SELECT * FROM proveedores ORDER BY nombre");
										while ($pro_o = mysqli_fetch_assoc($buscar_pro)) {
											$seleccionado = "";
											if ($pro_o['codigo'] == $pro_actual) {
												$seleccionado = "selected";
											}
											echo "<option value='" . $pro_o['codigo'] . "' " . $seleccionado . ">" . $pro_o['nombre'] . "</option>";
										}
										$seleccionado = "";
										?>
									</select>
								</div>
								<div class="mb-3">
									<label for="inputSuma" class="form-label label-act">La suma de</label>
									<div class="input-group mb-3">
										<input type="text" class="form-control form-act" name="monto" placeholder="$" id="inputSuma" autocomplete="off" onkeypress="soloDecimales(event)" onblur="mostrarMontoEnLetras()">
										<input type="text" class="form-control form-act input-disabled " id="inputSalida" disabled>
									</div>
								</div>
								<div class="mb-3 ">
									<label for="inputDetalle" class="form-label label-act">Detalle</label>
									<input type="text" class="form-control form-act" name="descripcion" id="inputDetalle" autocomplete="off" oninput="validarDetalle()">
								</div>
							</div>
						</div>
					</div>
					<!-------------------------------------------------------------- SEGUNDA COLUMNA ----------------------------------------------------------------------->
					<div class="col-md-6">
						<div class="card custom-subcard">
							<!--------------------------------------------------- SUBCARD TÍTULO --------------------------------------------------->
							<div class="card-header custom-subheader">Objetos de Gastos</div>
							<div class="card-body">
								<div class="mb-3">
									<label for="inputObjeto" class="form-label label-act">Objeto</label>
									<select type="text" class="form-control custom-select" name="objeto" id="inputObjeto">
										<!-- SELECT DE LA BD CONCILIACION - 'objeto_gasto' -->
										<?php 
											
											$buscar_obj = mysqli_query($est, "SELECT * FROM objeto_gasto ORDER BY codigo, detalle");
											while ($pro_obj = mysqli_fetch_assoc($buscar_obj)){
												$seleccionado="";
												if ($pro_obj['codigo'] == $probj_actual) {
													$seleccionado="selected";
												}
												echo "<option value='".$pro_obj['codigo']."' ".$seleccionado.">".$pro_obj['codigo']." - ".$pro_obj['detalle']."</option>";
											}
											$seleccionado="";
										?>
									</select>
								</div>
								<div class="mb-0">
									<label for="inputMonto" class="form-label label-act">Monto</label>
									<input type="text" class="form-control form-act" id="inputMonto" name="montoObjeto" autocomplete="off" onkeypress="soloDecimales(event)">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--------------------------------------------------------- CARD FOOTER - BOTONES --------------------------------------------------------->
			<div class="card-footer text-center custom-footer">
				<button type="submit" id="btnGrabar" name="grabar" class="btn btn-warning btn-footer mx-3">Grabar</button>
				<button type="reset" id="btnNuevo" class="btn btn-warning btn-footer mx-3">Nuevo</button>
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
if (isset($_POST['grabar'])) {
	if (strlen($_POST['numero_cheque']) >= 1 && strlen($_POST['fecha']) >= 1 && strlen($_POST['beneficiario']) >= 1 && strlen($_POST['monto']) >= 1 && strlen($_POST['descripcion']) >= 1 && strlen($_POST['objeto']) >= 1 && strlen($_POST['montoObjeto']) >= 1) {
		$numero_cheque = trim($_POST['numero_cheque']);
		$fecha = trim($_POST['fecha']);
		$beneficiario = trim($_POST['beneficiario']);
		$monto = trim($_POST['monto']);
		$descripcion = trim($_POST['descripcion']);
		$objeto = trim($_POST['objeto']);
		$montoObj = trim($_POST['montoObjeto']);

		// Consulta para verificar si el número de cheque ya existe
		$consulta_existencia = "SELECT * FROM cheques WHERE numero_cheque = '$numero_cheque'";
		$resultado_existencia = mysqli_query($est, $consulta_existencia);
		if (mysqli_num_rows($resultado_existencia) > 0) {
			echo "<script>alert('El número de cheque ya existe. Por favor, elige otro número de cheque.');</script>";
		} else {
			// Si el número de cheque no existe, procede con la inserción
			$consulta = "INSERT INTO cheques(numero_cheque, fecha, beneficiario, monto, descripcion, codigo_objeto1, monto_objeto1) VALUES ('$numero_cheque','$fecha', '$beneficiario','$monto', '$descripcion', '$objeto', '$montoObj')";
			$resultado = mysqli_query($est, $consulta);
			if ($resultado) {
				echo "<script>alert('Cheque registrado correctamente');</script>";
			} else {
				echo "<script>alert('Error al registrar el cheque');</script>";
			}
		}
	} else {
		echo "<script>alert('Por favor, completa todos los campos');</script>";
	}
}
?>