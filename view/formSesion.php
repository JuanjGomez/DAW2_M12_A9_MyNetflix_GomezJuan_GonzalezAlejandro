<?php
    session_start();
    if(isset($_SESSION['idUser'])){
        header('Location: ../index.php');
        exit();
    }
    if(isset($_SESSION['errorLogin']) && $_SESSION['errorLogin']){
        echo '<script>let errorLogin = true;</script>';
        unset($_SESSION['errorLogin']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css" integrity="sha256-ugbaEitpVSMgCpnPe7m69OyL6M47KkfE36OdRjQXD28=" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/forms.css">
    <title>Document</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png">
        </div>
    </header>
    <h1>Iniciar Sesion</h1>
    <div id="centrarDiv">
        <div id="formSesion">
            <form method="POST" action="../backend/verificarSesion.php">
                <label for="username">Username:<br>
                    <input type="text" name="username" id="username" placeholder="Username">
                </label>
                <br>
                <span class="error" id="errorUsername"></span><p></p>
                <label for="pwd">Contrasena:<br>
                    <input type="password" name="pwd" id="pwd" placeholder="contrasena">
                </label>
                <br>
                <span class="error" id="errorPwd"></span><p></p>
                <div id="btn-submit">
                    <button type="submit" id="btn-sesion" disabled>Iniciar</button>
                </div>
            </form>
            <br>
            <hr>
            <div id="centrarBotones">
                <a href="../index.php"><button id="btn-formInicio">Inicio</button></a>
                <a href="formRegistro.php"><button id="btn-formCombinado">Registrarse</button></a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="../js/comprobarSesion.js"></script>
</body>
</html>