<?php
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: ../view/formSesion.php');
        die();
    }

    require_once '../database/conexion.php';
    $username = trim($_POST['username']);
    $password = trim($_POST['pwd']);

    try{
        // Consulta para verificar si existe el usuaario
        $sqlUser = "SELECT * FROM tbl_usuarios WHERE username_u = :username AND password_u = :pwd";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtUser->bindParam(':pwd', $password, PDO::PARAM_STR);
        $stmtUser->execute();

        // Si existe el usuario, iniciar sesión y redireccionar a la página de inicio
        if($stmtUser->rowCount() > 0){
            // Inicio de sesión
            session_start();
            $_SESSION['idUser'] = $username;
            header('Location:../view/home.php');
            die();
        }

        // Si no existe el usuario, redireccionar a la página de inicio de sesión
        $_SESSION['errorLogin'] = true;
        header('Location:../view/formSesion.php');
        die();
    } catch (PDOException $e){
        echo "Error: ".$e->getMessage();
        die();
    }