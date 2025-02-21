<?php
session_start();
if (!isset($_SESSION['idUser']) || !in_array($_SESSION['rol'], ['administrador'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../database/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_usuario'])) {
    $id_u = $_POST['id_u'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    $id_rol = $_POST['id_rol'];

    // Actualizar datos
    $sql = "UPDATE tbl_usuarios SET username_u = ?, email_u = ?, activo_u = ?, id_rol = ? WHERE id_u = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username, $email, $activo, $id_rol, $id_u]);

    header('Location: ../view/gestionarUsuarios.php');
    exit();
} else {
    header('Location: ../view/gestionarUsuarios.php');
    exit();
}
