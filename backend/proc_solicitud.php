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
        $conn->beginTransaction();

        // Actualizar el estado de la solicitud a aprobado
        $querySolicitud = "UPDATE tbl_solicitudes_registro SET estado = 'aprobado' WHERE id_u = :id AND estado = 'pendiente'";
        $stmtSolicitud = $conn->prepare($querySolicitud);
        $stmtSolicitud->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        
        // Actualizar el estado del usuario a activo
        $queryUsuario = "UPDATE tbl_usuarios SET activo_u = TRUE WHERE id_u = :id";
        $stmtUsuario = $conn->prepare($queryUsuario);
        $stmtUsuario->bindParam(':id', $idUsuario, PDO::PARAM_INT);

        if ($stmtUsuario->execute() && $stmtSolicitud->execute()) {
            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            $conn->rollBack();
            echo json_encode(['success' => false, 'error' => 'Error al actualizar el usuario']);
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Solicitud no válida']);
}
?>