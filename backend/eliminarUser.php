<?php
session_start();
require_once '../database/conexion.php';

// Verificación de sesión y permisos
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}

// Verificar si se recibió el parámetro 'id' por GET
if (isset($_GET['id'])) {
    $id_u = $_GET['id'];
    
    // Eliminar el usuario de la base de datos
    $sql = "DELETE FROM tbl_usuarios WHERE id_u = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_u, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = "Usuario eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el usuario.";
    }
}

// Redirigir de vuelta al CRUD de usuarios
header('Location: ../view/gestionarUsuarios.php');
exit();