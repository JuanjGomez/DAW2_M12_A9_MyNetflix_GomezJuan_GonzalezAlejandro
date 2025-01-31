<?php
    session_start();
    if(isset($_SESSION['idUser'])){
        header('Location: ../index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <img>
    </header>
    <h1>Iniciar Sesion</h1>
    <div id="formSesion">
        <form method="POST" action="../backend/verificarSesion.php">
            <label for="username">Username:
                <input type="text" name="username" id="username" placeholder="Username">
            </label>
            <span id="errorUsername"></span>
            <br>
            <label for="pwd">Contrasena:
                <input type="text" name="pwd" id="pwd" placeholder="contrasena">
            </label>
            <span id="errorPwd"></span>
            <br>
            <button type="submit" id="btn-sesion" disabled>Iniciar</button>
        </form>
    </div>
    <script src="../js/comprobarSesion.js"></script>
</body>
</html>