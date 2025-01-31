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

    </header>
    <h1>REGISTRO</h1>
    <div id="formRegistro">
        <form method="POST">
            <label for="username">Username:
                <input type="text" id="username" name="username" placeholder="Ex: Joan123">
            </label>
            <span id="errorUser"></span>
            <br>
            <label for="email">Email:
                <input type="email" id="email" name="email" placeholder="Ex: example@gmail.com">
            </label>
            <span id="errorEmail"></span>
            <br>
            <label for="pwe">Contrasena: 
                <input type="password" id="pwd" name="pwd" placeholder="asdASD123">
            </label>
            <span id="errorPwd"></span>
            <label for="rPwd">Repetir Contrasena:
                <input type="password" id="rPwd" name="rPwd" placeholder="asdASD123">
            </label>
            <span id="errorRpwd"></span>
            <br>
            <button type="submit" id="btn-registro" disabled>Registrarse</button>
        </form>
    </div>
</body>
</html>