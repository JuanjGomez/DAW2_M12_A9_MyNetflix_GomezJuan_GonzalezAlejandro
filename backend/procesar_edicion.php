<?php
session_start();
if (!isset($_SESSION['idUser']) || !in_array($_SESSION['rol'], ['administrador'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../database/conexion.php';

try {
    if (!isset($_POST['id_u'])) {
        throw new Exception('ID de usuario no proporcionado');
    }

    $id_usuario = $_POST['id_u'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $activo = isset($_POST['activo']) ? $_POST['activo'] : 0;
    $id_rol = $_POST['id_rol'];

    // Iniciar transacción
    $conn->beginTransaction();

    // Verificar si hay cambio de contraseña
    if (!empty($_POST['password']) && !empty($_POST['password2'])) {
        if ($_POST['password'] !== $_POST['password2']) {
            throw new Exception('Las contraseñas no coinciden');
        }
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Actualizar con nueva contraseña
        $sql = "UPDATE tbl_usuarios SET 
                username_u = :username,
                email_u = :email,
                password_u = :password,
                activo_u = :activo,
                id_rol = :id_rol
                WHERE id_u = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':password', $password_hash);
    } else {
        // Actualizar sin cambiar la contraseña
        $sql = "UPDATE tbl_usuarios SET 
                username_u = :username,
                email_u = :email,
                activo_u = :activo,
                id_rol = :id_rol
                WHERE id_u = :id";
        
        $stmt = $conn->prepare($sql);
    }

    // Manejar el estado activo y las solicitudes
    if ($activo == 0) {
        // Si se está desactivando el usuario
        $sqlCheck = "SELECT id_soli FROM tbl_solicitudes_registro 
                    WHERE id_u = :id_usuario AND estado = 'aprobado'";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute([':id_usuario' => $id_usuario]);
        
        if ($stmtCheck->rowCount() > 0) {
            // Actualizar solicitud existente a rechazado
            $sqlUpdate = "UPDATE tbl_solicitudes_registro 
                         SET estado = 'rechazado' 
                         WHERE id_u = :id_usuario AND estado = 'aprobado'";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->execute([':id_usuario' => $id_usuario]);
        }
    } else if ($activo == 1) {
        // Si se está activando el usuario
        $sqlCheck = "SELECT id_soli, estado FROM tbl_solicitudes_registro 
                    WHERE id_u = :id_usuario 
                    AND (estado = 'pendiente' OR estado = 'rechazado')";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute([':id_usuario' => $id_usuario]);
        
        if ($stmtCheck->rowCount() > 0) {
            // Actualizar solicitud existente a aprobado
            $sqlUpdate = "UPDATE tbl_solicitudes_registro 
                         SET estado = 'aprobado' 
                         WHERE id_u = :id_usuario";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->execute([':id_usuario' => $id_usuario]);
        } else {
            // Insertar nueva solicitud aprobada
            $sqlInsert = "INSERT INTO tbl_solicitudes_registro (id_u, estado) 
                         VALUES (:id_usuario, 'aprobado')";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->execute([':id_usuario' => $id_usuario]);
        }
    }

    // Ejecutar la actualización del usuario
    $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':activo' => $activo,
        ':id_rol' => $id_rol,
        ':id' => $id_usuario
    ]);

    // Confirmar transacción
    $conn->commit();

    $_SESSION['success'] = 'Usuario actualizado correctamente';
    header('Location: ../view/gestionarUsuarios.php');

} catch (Exception $e) {
    // Revertir cambios si hay error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../view/editarUsuario.php?id=' . $id_usuario);
    exit();
}
?>
