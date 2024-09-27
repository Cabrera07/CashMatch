
// --------------------------------------------FUNCIONES PARA VALIDACION DE CHEQUES--------------------------------------------//

// VALIDAR NUMERO DE CHEQUE
function validarNoCheque() {
    var noChequeInput = document.getElementById("inputNoCheque");
    var noCheque = noChequeInput.value.trim();
    if (!/^(\d+)?(\.\d*)?$/.test(noCheque)) {
        // Muestra la alerta
        document.getElementById("alertaNoCheque").style.display = "block";
        // Si hay más de un punto decimal, elimina todos menos el primero
        noChequeInput.value = noCheque.replace(/[^\d.]/g, '').replace(/^(\d*\.?)|(\.\d*)$/g, '$1$2');
    } else {
        // Oculta la alerta si el valor es válido
        document.getElementById("alertaNoCheque").style.display = "none";
    }
}

// VALIDAR LA SUMA DEL CHEQUE
function soloDecimales(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    var input = evt.target.value;

    // Permitir solo un punto decimal
    if (charCode === 46 && input.indexOf('.') !== -1) {
        evt.preventDefault();
        mostrarAlerta("Este campo solo acepta un punto decimal");
    }

    // Permitir solo dígitos y un punto decimal
    if (charCode !== 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        evt.preventDefault();
        mostrarAlerta("Este campo solo se acepta números");
    }
}
function mostrarAlerta(mensaje) {
    var alerta = document.getElementById("alertaDecimales");
    var mensajeAlerta = document.getElementById("mensajeAlerta");
    mensajeAlerta.textContent = mensaje;
    alerta.style.display = "block";
}

// VALIDAR DETALLE CHEQUES
function validarDetalle() {
    var detalleInput = document.getElementById("inputDetalle");
    var detalle = detalleInput.value.trim();
    if (!/^[a-zA-ZáéíóúüÁÉÍÓÚÜ\s.,?¿/]+$/.test(detalle)) {
        mostrarAlertaDetalle("Este campo no acepta numeros");
        detalleInput.value = detalle.replace(/[^a-zA-ZáéíóúüÁÉÍÓÚÜ\s.,?¿/]/g, '');
    }
}
function mostrarAlertaDetalle(mensaje) {
    var alertaDetalle = document.getElementById("alertaDetalle");
    var mensajeAlertaDetalle = document.getElementById("mensajeAlertaDetalle");
    mensajeAlertaDetalle.textContent = mensaje;
    alertaDetalle.style.display = "block";
}

// FUNCIONES DE NUMEROS A LETRAS 
function numeroALetras(numero) {
    const unidades = ['', 'Uno', 'Dos', 'Tres', 'Cuatro', 'Cinco', 'Seis', 'Siete', 'Ocho', 'Nueve'];
    const especiales = ['Diez', 'Once', 'Doce', 'Trece', 'Catorce', 'Quince', 'Dieciséis', 'Diecisiete', 'Dieciocho', 'Diecinueve'];
    const decenas = ['', '', 'Veinte', 'Treinta', 'Cuarenta', 'Cincuenta', 'Sesenta', 'Setenta', 'Ochenta', 'Noventa'];
    const centenas = ['', 'Ciento', 'Doscientos', 'Trescientos', 'Cuatrocientos', 'Quinientos', 'Seiscientos', 'Setecientos', 'Ochocientos', 'Novecientos'];
    const miles = ['', 'Mil', 'Millón'];

    let letras = '';

    if (numero >= 1000000) {
        letras += numeroALetras(Math.floor(numero / 1000000)) + ' ' + miles[2] + ' ';
        numero %= 1000000;
    }
    if (numero >= 1000) {
        if (numero >= 1000 && numero <= 1999) {
            letras += 'Mil ';
        } else if (numero >= 2000 && numero <= 9999) {
            letras += numeroALetras(Math.floor(numero / 1000)) + ' ' + miles[1] + ' ';
        } else {
            letras += numeroALetras(Math.floor(numero / 1000)) + ' ' + miles[1] + ' ';
        }
        numero %= 1000;
    }
    if (numero >= 100) {
        if (numero === 100) {
            letras += 'Cien ';
        } else {
            letras += centenas[Math.floor(numero / 100)] + ' ';
        }
        numero %= 100;
    }
    if (numero >= 10 && numero <= 19) {
        letras += especiales[numero - 10];
        numero = 0; 
    } else if (numero >= 10) {
        letras += decenas[Math.floor(numero / 10)] + ' ';
        numero %= 10;
    }
    if (numero > 0) {
        letras += unidades[numero];
    }
    return letras.trim(); 
}
// MOSTRAR LA SUMA EN LETRAS
function mostrarMontoEnLetras() {
    var monto = document.getElementById("inputSuma").value;
    var parteEntera = Math.floor(monto);
    var parteDecimal = Math.round((monto - parteEntera) * 100);
    var montoEnLetras = numeroALetras(parteEntera) + ' Balboas con ' + (parteDecimal < 10 ? '0' : '') + parteDecimal + '/100';
    document.getElementById("inputSalida").value = montoEnLetras;
}
//VALIDAR QUE LOS VALORES SEAN IGUALES EN SUMA Y MONTO
function validarSuma() {
    // Captura los valores de "La suma de" y "Monto"
    var sumaDe = parseFloat(document.getElementById('inputSuma').value);
    var monto = parseFloat(document.getElementById('inputMonto').value);

    // Comprueba si los valores son iguales
    if (sumaDe !== monto) {
        // Muestra un mensaje de error al usuario
        alert("El valor ingresado en 'La suma de' no coincide con el 'Monto'. Por favor, verifique.");

        // Evita que el formulario se envíe
        return false;
    }

    // Si los valores son iguales, permite que el formulario se envíe
    return true;
}

// --------------------------------------------FUNCIONES PARA ANULACION DE CHEQUES--------------------------------------------//

function validarDetalledeAnulacion() {
    var detalleInput = document.getElementById("inputdAnul");
    var detalle = detalleInput.value.trim();
    if (!/^[a-zA-ZáéíóúüÁÉÍÓÚÜ\s.,?¿/]+$/.test(detalle)) {
        alert("El detalle solo puede contener letras, incluyendo vocales con acentuación, diéresis, y los caracteres .,?¿/");
        detalleInput.value = detalle.replace(/[^a-zA-ZáéíóúüÁÉÍÓÚÜ\s.,?¿/]/g, '');
    }
}

// --------------------------------------------FUNCIONES PARA CONCILIACON BANCARIA--------------------------------------------//
// FUNCION PARA EL BOTON NUEVO
function resetForm() {
    // Seleccionar el formulario por su ID
    var form = document.getElementById('conciliacionForm');

    // Restablecer los valores de los campos del formulario
    form.reset();
}
