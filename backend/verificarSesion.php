<?php
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: ../view/formSesion.php');
        die();
    }

    require_once '../database/conexion.php';
    session_start();

    $username = trim($_POST['username']);
    $password = trim($_POST['pwd']);

    try{
        // Consulta para verificar si existe el usuaario
        $sqlUser = "SELECT u.*, r.nombre_rol 
                    FROM tbl_usuarios u 
                    INNER JOIN tbl_roles r ON u.id_rol = r.id_rol 
                    WHERE u.username_u = :username";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtUser->execute();
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe
        if(!$user){
            $_SESSION['errorLogin'] = true;
            header('Location:../view/formSesion.php');
            die();
        }

        // Verificar contrasena hasheada
        if($user && password_verify($password, $user['password_u'])){
            $_SESSION['idUser'] = $user['id_u'];
            $_SESSION['username'] = $user['username_u'];
            $_SESSION['rol'] = $user['nombre_rol'];
            $_SESSION['successLogin'] = true; // SweetAlert

            if($user['nombre_rol'] === 'administrador'){
                header('Location:../view/admin.php');
                exit();
            } else if($user['nombre_rol'] === 'usuario'){
                header('Location: ../index.php');
                exit();
            }
        } else {
            $_SESSION['errorLogin'] = true;
            header('Location:../view/formSesion.php');
            die();
        }
    } catch (PDOException $e){
        echo "Error: ".$e->getMessage();
        die();
    }