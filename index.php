<?php
    session_start();
    if(isset($_SESSION['successLogin']) && $_SESSION['successLogin']){
        echo '<script>let successLogin = true</script>';
        unset($_SESSION['successLogin']);
    }
    if(isset($_SESSION['successCrear']) && $_SESSION['successCrear']){
        echo '<script>let successCrear = true</script>';
        unset($_SESSION['successCrear']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css" integrity="sha256-ugbaEitpVSMgCpnPe7m69OyL6M47KkfE36OdRjQXD28=" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/inicio.css">
    <title>Document</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="img/logoN.png">
        </div>
    </header>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="../js/toolsInicio.js"></script>
</body>
</html>