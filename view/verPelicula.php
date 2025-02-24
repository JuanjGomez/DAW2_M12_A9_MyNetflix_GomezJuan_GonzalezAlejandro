<?php
    require_once '../database/conexion.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/pelicula.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css" integrity="sha256-ugbaEitpVSMgCpnPe7m69OyL6M47KkfE36OdRjQXD28=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <?php if(isset($_SESSION['idUser']) && $_SESSION['idUser'] != null) : ?>
                    <a href="../backend/cerrarSesion.php">Cerrar Sesión</a>
                <?php else : ?>
                    <a href="formSesion.php">Inicio Sesión</a>
                    <a href="formRegistro.php">Registro</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="left">
            <div class="boton-salida">
                <div class="img-salida">
                    <a href="../index.php"><img src="../img/left.png"></a>
                </div>
            </div>
            <div class="portada-img">
                
            </div>
        </div>
        <div class="right">

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="../js/toolsInicio.js"></script>
</body>
</html>