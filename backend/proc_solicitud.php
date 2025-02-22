<?php
session_start();
require_once '../database/conexion.php'; // Ajusta la ruta según tu estructura

// Verificación de sesión y permisos
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $idUsuario = $_POST['id'];

    try {
        // Actualizar el estado del usuario a activo
        $query = "UPDATE tbl_usuarios SET activo_u = TRUE WHERE id_u = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar el usuario']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Solicitud no válida']);
}
?>