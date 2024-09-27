<!----------------------------------------------------------- BARRA DE NAVEGACION ----------------------------------------------------------->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand ms-0">
            <img src="../img/horizontal_logo.png" alt="cashmatch" width="200" height="70">
        </a>

        <!-----RESPONSIVE BUTTON----->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!--------------------------------------------------- NAVBAR TABS --------------------------------------------------->
        <div class="collapse navbar-collapse justify-content-sm-center justify-content-lg-start" id="navbarNavDropdown">
            <ul class="navbar-nav nav-tabs ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link px-2 px-lg-1" href="index.php">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 px-lg-1" href="cheque.php">Cheques</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-2 px-lg-1" href="#" role="button" data-bs-toggle="dropdown">Operaciones con Cheques</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="anulacion.php">Anulación</a></li>
                        <li><a class="dropdown-item" href="circulacion.php">Sacar de Circulación</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 px-lg-1" href="transacciones.php">Otras Transacciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 px-lg-1" href="conciliacion.php">Conciliación</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 px-lg-1" href="reportes.php">Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 px-lg-1" href="mantenimiento.php">Mantenimiento</a>
                </li>
            </ul>
        </div>
    </div>
</nav>