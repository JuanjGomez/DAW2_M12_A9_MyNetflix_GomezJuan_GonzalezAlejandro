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
        $sqlUser = "SELECT u.*, r.nombre_rol 
                    FROM tbl_usuarios u 
                    INNER JOIN tbl_roles r ON u.id_rol = r.id_rol 
                    WHERE u.username_u = :username";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtUser->execute();
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        session_start();

        // Verificar si el usuario existe
        if(!$user){
            $_SESSION['errorLogin'] = true;
            header('Location:../view/formSesion.php');
            die();
        }

        // Verificar contrasena hasheada
        if($user && password_verify($password, $user['password_u'])){
            $_SESSION['idUser'] = $user['id_u'];
            if($user['nombre_rol'] == 'administrador'){
                $_SESSION['rol'] = 'administrador';
                header('Location:../view/admin.php');
                exit();
            } else if($user['nombre_rol'] == 'usuario'){
                $_SESSION['rol'] = 'usuario';
                header('Location: ../index.php');
                exit();
            }
        }
    } catch (PDOException $e){
        echo "Error: ".$e->getMessage();
        die();
    }