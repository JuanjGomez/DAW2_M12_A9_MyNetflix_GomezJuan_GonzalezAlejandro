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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css" integrity="sha256-ugbaEitpVSMgCpnPe7m69OyL6M47KkfE36OdRjQXD28=" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/usuarios.css">
    <title>Gestionar Usuarios</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png">
        </div>
    </header>
    <div class="">
        <h3>Solicitudes</h3>
        <div>
            
        </div>
    </div>
    <div>
        <h3>Usuarios</h3>
        <div>
            <table id="usuariosTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Mostrar usuarios -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="../js/toolsUsuarios.js"></script>
</body>
</html>