<?php
session_start();
require_once '../database/conexion.php';

try {
    // Verificar que se recibieron todos los campos necesarios
    if (!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['id_rol'])) {
        $_SESSION['error'] = 'Faltan datos requeridos';
        header('Location: ../view/formUsuarios.php');
        exit();
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $id_rol = $_POST['id_rol'];
    // Convertir el valor de activo a 1 o 0
    $activo = isset($_POST['activo']) ? 1 : 0;

    // Verificar duplicados en username y email
    $stmt = $conn->prepare("SELECT username_u, email_u FROM tbl_usuarios WHERE username_u = :username OR email_u = :email");
    $stmt->execute([
        ':username' => $username,
        ':email' => $email
    ]);
    
    $existente = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existente) {
        $errores = [];
        if ($existente['username_u'] === $username) {
            $errores[] = "El nombre de usuario ya existe";
        }
        if ($existente['email_u'] === $email) {
            $errores[] = "El email ya está registrado";
        }
        $_SESSION['error'] = implode(" y ", $errores);
        header('Location: ../view/formUsuarios.php');
        exit();
    }

    // Si no hay duplicados, crear el usuario
    $sql = "INSERT INTO tbl_usuarios (username_u, email_u, password_u, activo_u, id_rol) 
            VALUES (:username, :email, :password, :activo, :id_rol)";
    
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':activo' => $activo,
        ':id_rol' => $id_rol
    ]);

    if ($result) {
        $_SESSION['success'] = 'Usuario creado correctamente';
        header('Location: ../view/gestionarUsuarios.php');
    } else {
        $_SESSION['error'] = 'Error al crear el usuario';
        header('Location: ../view/formUsuarios.php');
    }

} catch (Exception $e) {
    $_SESSION['error'] = 'Error: ' . $e->getMessage();
    header('Location: ../view/formUsuarios.php');
}
exit();
?>