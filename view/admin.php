<?php
    session_start();
    if(!isset($_SESSION['idUser']) || !in_array($_SESSION['rol'], ['administrador'])){
        header('Location:../index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png">
        </div>
        <div class="user-dropdown">
            <button class="dropbtn">
                <i class="fas fa-user"></i>
            </button>
            <div class="dropdown-content">
                <a href="../backend/cerrarSesion.php">Cerrar Sesión</a>
            </div>
        </div>
    </header>
    <div class="container">
        <h1 class="bienvenido">Bienvenido, <?php echo $_SESSION['username']; ?>!</h1>
        <h3>Selecciona algún área de trabajo:</h3>
        <div class="row">
            <div class="col-md-3">
                <a href="gestionarPeliculas.php" class="botonImg">
                    <img src="../img/peliculas.png" alt="Ir a Gestionar Peliculas">
                </a>
                <p>Gestionar Peliculas</p>
            </div>
            <div class="col-md-3">
                <div class="container_img grow">
                    <a href="gestionarUsuarios.php" class="botonImg">
                        <img src="../img/solicitudes.png" alt="Ir a Gestionar Usuarios">
                    </a>
                </div>
                <p>Gestionar Usuarios</p>
            </div>
            <!-- Nueva sección Gestión de Solicitudes -->
            <div class="col-md-3">
                <div class="container_img grow">
                    <a href="gestionSolicitudes.php" class="botonImg">
                        <img src="../img/solicitudes.png" alt="Ir a Gestionar Solicitudes">
                    </a>
                </div>
                <p>Gestión de Solicitudes</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
</body>
</html>
