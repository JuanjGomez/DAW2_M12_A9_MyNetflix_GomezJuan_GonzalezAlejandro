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
    // Verificar el estado de la solicitud actual
    $checkQuery = "SELECT estado 
                  FROM tbl_solicitudes_registro 
                  WHERE id_u = :idUser";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([':idUser' => $_SESSION['idUser']]);
    $solicitudActual = $checkStmt->fetch(PDO::FETCH_ASSOC);

    // Si no hay solicitud o la última fue rechazada, crear una nueva
    if (!$solicitudActual || $solicitudActual['estado'] === 'rechazado') {
        $query = "INSERT INTO tbl_solicitudes_registro (id_u, estado) 
                 VALUES (:idUser, 'pendiente')";
        $stmt = $conn->prepare($query);
        $stmt->execute([':idUser' => $_SESSION['idUser']]);

        echo json_encode([
            'success' => true,
            'message' => 'Solicitud enviada correctamente'
        ]);
    } else {
        // Si hay una solicitud pendiente o aprobada
        echo json_encode([
            'success' => false,
            'error' => $solicitudActual['estado'],
            'message' => $solicitudActual['estado'] === 'pendiente' 
                ? 'Tu solicitud está en proceso de revisión' 
                : 'Tu cuenta ya está activada'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
    ]);
}
