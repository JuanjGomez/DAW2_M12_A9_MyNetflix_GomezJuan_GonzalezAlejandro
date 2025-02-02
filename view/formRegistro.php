<?php
    session_start();
    if(isset($_SESSION['idUser'])){
        header('Location: ../index.php');
        exit();
    }
    if(isset($_SESSION['esperaPeticion']) && $_SESSION['esperaPeticion']){
        echo '<script>let esperaPeticion = true; </script>';
        unset($_SESSION['esperaPeticion']);
    }
    if(isset($_SESSION['errorCrear']) && $_SESSION['errorCrear']){
        echo '<script>let errorCrear = true; </script>';
        unset($_SESSION['errorCrear']);
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
    <h1>REGISTRO</h1>
    <div id="centrarDiv">
        <div id="formRegistro">
            <form method="POST" action="../backend/insertUser.php">
                <div class="form-container">
                    <div class="form-column">
                        <label for="username">Username:<br>
                            <input type="text" id="username" name="username" placeholder="Ex: Joan123" value="<?php echo $_SESSION['usernameDuplicado'] ?? ''; ?>">
                        </label>
                        <span class="error" id="errorUsername"></span>
                        <label for="email">Email:<br>
                            <input type="email" id="email" name="email" placeholder="Ex: example@gmail.com" value="<?php echo $_SESSION['emailDuplicado'] ?? '' ?>">
                        </label>
                        <span class="error" id="errorEmail"></span>
                    </div>
                    <div class="form-column">
                        <label for="pwe">Contrasena: <br>
                            <input type="password" id="pwd" name="pwd" placeholder="asdASD123">
                        </label>
                        <span class="error" id="errorPwd"></span>
                        <label for="rPwd">Repetir Contrasena: <br>
                            <input type="password" id="rPwd" name="rPwd" placeholder="asdASD123">
                        </label>
                        <span class="error" id="errorRpwd"></span>
                    </div>
                </div>
                <div id="btn-submit">
                    <button type="submit" id="btn-sesion" disabled>Registrarse</button>
                </div>
                <br>
                <hr>
            </form>
            <div id="centrarBotones">
                <a href="../index.php"><button id="btn-formInicio">Inicio</button></a>
                <a href="formSesion.php"><button id="btn-formCombinado">Iniciar Sesion</button></a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="../js/comprobarRegistro.js"></script>
</body>
</html>