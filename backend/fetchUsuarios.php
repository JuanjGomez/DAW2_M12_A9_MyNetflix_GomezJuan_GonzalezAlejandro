<?php
session_start();
require_once '../database/conexion.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $busqueda = $data['busqueda'] ?? '';
    $estado = $data['estado'] ?? 'todos';
    
    $sql = "SELECT u.id_u, u.username_u, u.email_u, u.activo_u, r.nombre_rol 
            FROM tbl_usuarios u 
            JOIN tbl_roles r ON u.id_rol = r.id_rol
            WHERE (u.username_u LIKE :busqueda OR u.email_u LIKE :busqueda)";
    
    if ($estado === 'activos') {
        $sql .= " AND u.activo_u = 1";
    } elseif ($estado === 'inactivos') {
        $sql .= " AND u.activo_u = 0";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':busqueda' => "%$busqueda%"
    ]);
    
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'usuarios' => $usuarios
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}