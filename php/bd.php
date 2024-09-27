<?php 
    // ----------------------------------- CONEXION A BD COMPUTO -----------------------------------//
    $host = "localhost";
    $user = "d52024";
    $pass = "12345";
    $db = "computo";
    
    // CONEXION A LA BD
    $est = mysqli_connect($host, $user, $pass, $db);

    // VERIFICACION DE CONEXION 
    if (mysqli_connect_errno()) {
        die("Fallo la conexión a MySQL: " . mysqli_connect_errno() . " " . mysqli_connect_error());
    }
    
    $est->set_charset('utf8');
?>