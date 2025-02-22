<?php
session_start();
require_once '../database/conexion.php';

// Verificación de sesión y permisos
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_usuario'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $id_rol = $_POST['id_rol'];

    // Validar si el usuario ya existe
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_usuarios WHERE email_u = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $existeUsuario = $stmt->fetchColumn();

    if ($existeUsuario > 0) {
        $_SESSION['error'] = "El correo electrónico ya está registrado.";
        header('Location: gestionarUsuarios.php');
        exit();
    }

    // Insertar nuevo usuario
    $sql = "INSERT INTO tbl_usuarios (username_u, email_u, password_u, activo_u, id_rol) 
            VALUES (:username, :email, :password, :activo, :id_rol)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);
    $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Usuario creado exitosamente.";
    } else {
        $_SESSION['error'] = "Error al crear el usuario.";
    }

    header('Location: ../view/gestionarUsuarios.php');
    exit();
}