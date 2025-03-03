<?php
session_start();
header('Content-Type: application/json');

require_once '../database/conexion.php';

// Verificar si el usuario está logueado
if (isset($_SESSION['idUser'])) {
    try {
        // Consultar el estado actual del usuario y su solicitud
        $query = "SELECT u.activo_u, 
                        COALESCE(
                            (SELECT estado 
                             FROM tbl_solicitudes_registro 
                             WHERE id_u = u.id_u
                            ), 'none'
                        ) as estado_solicitud
                 FROM tbl_usuarios u 
                 WHERE u.id_u = :id";
                 
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $_SESSION['idUser']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'isLoggedIn' => true,
            'activo' => $usuario['activo_u'],
            'estadoSolicitud' => $usuario['estado_solicitud'],
            'userId' => $_SESSION['idUser']
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'isLoggedIn' => true,
            'error' => 'Error al verificar estado: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'isLoggedIn' => false
    ]);
}
