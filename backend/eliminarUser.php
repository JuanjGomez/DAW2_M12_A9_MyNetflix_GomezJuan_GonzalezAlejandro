<?php
session_start();
require_once '../database/conexion.php';
header('Content-Type: application/json');

try {
    // Obtener el ID del usuario
    $datos = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($datos['id_usuario'])) {
        throw new Exception('ID de usuario no proporcionado');
    }

    $id_usuario = $datos['id_usuario'];

    // Iniciar transacción
    $conn->beginTransaction();

    try {
        // 1. Primero eliminar registros en tbl_solicitudes_registro
        $sql_solicitudes = "DELETE FROM tbl_solicitudes_registro WHERE id_u = :id_usuario";
        $stmt_solicitudes = $conn->prepare($sql_solicitudes);
        $stmt_solicitudes->execute([':id_usuario' => $id_usuario]);

        // 2. Finalmente eliminar el usuario
        $sql_usuario = "DELETE FROM tbl_usuarios WHERE id_u = :id_usuario";
        $stmt_usuario = $conn->prepare($sql_usuario);
        $stmt_usuario->execute([':id_usuario' => $id_usuario]);

        // Si todo sale bien, confirmar la transacción
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Usuario y sus registros eliminados correctamente'
        ]);

    } catch (Exception $e) {
        // Si hay algún error, revertir los cambios
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al eliminar: ' . $e->getMessage()
    ]);
}