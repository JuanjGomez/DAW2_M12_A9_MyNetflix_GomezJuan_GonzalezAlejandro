<?php
session_start();
require_once '../database/conexion.php';
header('Content-Type: application/json');

try {
    // Construir la consulta base
    $sql = "SELECT u.*, r.nombre_rol 
            FROM tbl_usuarios u 
            LEFT JOIN tbl_roles r ON u.id_rol = r.id_rol 
            WHERE 1=1";
    $params = [];

    // Aplicar filtro de búsqueda
    if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
        $busqueda = $_GET['busqueda'];
        $sql .= " AND (u.username_u LIKE :busqueda 
                      OR u.email_u LIKE :busqueda)";
        $params[':busqueda'] = "%$busqueda%";
    }

    // Aplicar filtro de estado
    if (isset($_GET['estado']) && $_GET['estado'] !== '') {
        $sql .= " AND u.activo_u = :estado";
        $params[':estado'] = $_GET['estado'];
    }

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'usuarios' => $usuarios
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}