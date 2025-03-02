<?php
session_start();
require_once '../database/conexion.php';
header('Content-Type: application/json');

if (!isset($_SESSION['idUser'])) {
    echo json_encode([
        'success' => false,
        'error' => 'No hay sesión activa'
    ]);
    exit;
}

try {
    // Verificar si ya existe una solicitud pendiente
    $checkQuery = "SELECT id_solicitud FROM tbl_solicitudes_reactivacion 
                  WHERE id_u = :idUser AND estado = 'pendiente'";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([':idUser' => $_SESSION['idUser']]);

    if ($checkStmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Ya tienes una solicitud de reactivación pendiente'
        ]);
        exit;
    }

    // Insertar nueva solicitud
    $query = "INSERT INTO tbl_solicitudes_reactivacion (id_u, fecha_solicitud, estado) 
              VALUES (:idUser, NOW(), 'pendiente')";
    $stmt = $conn->prepare($query);
    $stmt->execute([':idUser' => $_SESSION['idUser']]);

    echo json_encode([
        'success' => true,
        'message' => 'Solicitud enviada correctamente'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ]);
}
