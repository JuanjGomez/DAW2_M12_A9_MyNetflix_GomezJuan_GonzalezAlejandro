<?php
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location: ../view/formRegistro.php');
        die();
    }

    session_start();
    require_once '../database/conexion.php';

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['pwd']);
    $hashPwd = password_hash($password, PASSWORD_BCRYPT);

    try{
        $conn->beginTransaction();

        // Variable predeterminada
        $rol = 2;
        $estadoSolicitud = 'pendiente';

        // Verificar si el usuario tiene la peticion de solicitud pendiente
        $sqlSolicitudPendiente = "SELECT * 
                                FROM tbl_usuarios u 
                                INNER JOIN tbl_solicitudes_registro sr 
                                ON u.id_u = sr.id_u 
                                WHERE u.username_u = :username AND sr.estado = :solicitud";
        $stmtSolicitudPendiente = $conn->prepare($sqlSolicitudPendiente);
        $stmtSolicitudPendiente->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtSolicitudPendiente->bindParam(':solicitud', $estadoSolicitud, PDO::PARAM_STR);
        $stmtSolicitudPendiente->execute();
        $solicitudPendiente = $stmtSolicitudPendiente->fetch(PDO::FETCH_ASSOC);
        
        // Si hay una solicitud pendiente, redireccionar a la página de inicio
        if($solicitudPendiente){
            $_SESSION['esperaPeticion'] = true;
            header('Location:../view/formRegistro.php');
            die();
        }

        // Verificar si hay un username ya en la base de datos
        $sqlVerificarDuplicados = "SELECT *
                                    FROM tbl_usuarios 
                                    WHERE username_u = :username";
        $stmtVerificarDuplicados = $conn->prepare($sqlVerificarDuplicados);
        $stmtVerificarDuplicados->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtVerificarDuplicados->execute();
        $usuarioDuplicado = $stmtVerificarDuplicados->fetch(PDO::FETCH_ASSOC);

        if($usuarioDuplicado){
            $_SESSION['errorCrear'] = true;
            header('Location:../view/formRegistro.php');
            die();
        }

        // Insert de user 
        $sqlInsertUser = "INSERT INTO tbl_usuarios (username_u, email_u, password_u, id_rol)
                            VALUES (:username, :email, :pwd, :rol)";
        $stmtInsertUser = $conn->prepare($sqlInsertUser);
        $stmtInsertUser->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtInsertUser->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtInsertUser->bindParam(':pwd', $hashPwd, PDO::PARAM_STR);
        $stmtInsertUser->bindParam(':rol', $rol, PDO::PARAM_INT);
        $stmtInsertUser->execute();

        // Obtener el id del usuario recién creado
        $idUser = $conn->lastInsertId();

        // Insert de solicitud
        $sqlInsertSolicitud = "INSERT INTO tbl_solicitudes_registro (id_u, estado) 
                                VALUES (:idUser)";
        $stmtInsertSolicitud = $conn->prepare($sqlInsertSolicitud);
        $stmtInsertSolicitud->bindParam(':idUser', $idUser, PDO::PARAM_INT);
        $stmtInsertSolicitud->execute();

        // Confirmar transacción
        $conn->commit();

        // Redirección a la página de inicio
        $_SESSION['successCrear'] = true;
        header('Location:../index.php');
        
    }catch(Exception $e){
        // Deshacer transacción en caso de error
        $conn->rollBack();
        echo "Error: ". $e->getMessage();
        die();
    }