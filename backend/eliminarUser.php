<?php

// Verificación de sesión y permisos
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}

// Manejo de eliminación de usuario
if (isset($_GET['eliminar'])) {
    $id_u = $_GET['eliminar'];
    $sql = "DELETE FROM tbl_usuarios WHERE id_u = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_u, PDO::PARAM_INT);
    $stmt->execute();
}