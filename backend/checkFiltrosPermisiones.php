<?php
session_start();
header('Content-Type: application/json');

// Si no existe la variable de sesión 'activo', intentamos obtenerla de la base de datos
if (isset($_SESSION['idUser']) && !isset($_SESSION['actividad'])) {
    require_once '../database/conexion.php';
    
    try {
        $query = "SELECT activo_u FROM tbl_usuarios WHERE id_u = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $_SESSION['idUser']]);
        
        if ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['actividad'] = $usuario['activo_u'];
        }
    } catch (PDOException $e) {
        // Si hay error, log pero no interrumpir
        error_log("Error al obtener estado activo: " . $e->getMessage());
    }
}

$hasPermission = isset($_SESSION['idUser']) && isset($_SESSION['actividad']) && $_SESSION['actividad'] == 1;

echo json_encode([
    'hasPermission' => $hasPermission,
    'debug' => [
        'idUser' => isset($_SESSION['idUser']) ? $_SESSION['idUser'] : null,
        'actividad' => isset($_SESSION['actividad']) ? $_SESSION['actividad'] : null
    ]
]);