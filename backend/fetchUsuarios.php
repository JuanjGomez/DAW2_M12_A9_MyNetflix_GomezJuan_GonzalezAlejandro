<?php
session_start();
require_once '../database/conexion.php';

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : "";
$estado = isset($_GET['estado']) ? $_GET['estado'] : "todos";

try {
    $sql = "SELECT u.id_u, u.username_u, u.email_u, u.activo_u, r.nombre_rol 
            FROM tbl_usuarios u 
            JOIN tbl_roles r ON u.id_rol = r.id_rol
            WHERE u.username_u LIKE :filtro";
    
    if ($estado !== "todos") {
        $sql .= " AND u.activo_u = :estado";
    }

    $stmt = $conn->prepare($sql);
    $params = ['filtro' => "%$filtro%"];
    
    if ($estado !== "todos") {
        $params['estado'] = ($estado === "activos") ? 1 : 0;
    }

    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($usuarios);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
