<?php
session_start();
require_once '../database/conexion.php';

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(['error' => 'Acceso no autorizado']);
    exit();
}

$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : "";

try {
    $query = "SELECT u.id_u, u.username_u, u.email_u, u.activo_u, r.nombre_rol, s.id_soli 
            FROM tbl_usuarios u
            INNER JOIN tbl_roles r ON u.id_rol = r.id_rol 
            INNER JOIN tbl_solicitudes_registro s ON u.id_u = s.id_u
            WHERE s.estado = 'pendiente'
            AND u.username_u LIKE :filtro";
            
    $stmt = $conn->prepare($query);
    $stmt->execute(['filtro' => "%$filtro%"]);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($usuarios);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}