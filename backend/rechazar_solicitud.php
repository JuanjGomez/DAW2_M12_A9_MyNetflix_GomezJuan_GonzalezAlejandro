<?php
session_start();
require_once '../database/conexion.php';

if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    echo json_encode(['success' => false, 'error' => 'Acceso no autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['solicitud_id'])) {
    $idUsuario = $_POST['id'];
    $idSolicitud = $_POST['solicitud_id'];

    try {
        $query = "UPDATE tbl_solicitudes_registro 
                  SET estado = 'rechazado' 
                  WHERE id_u = :id AND id_soli = :solicitud_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $idUsuario, PDO::PARAM_INT);
        $stmt->bindParam(':solicitud_id', $idSolicitud, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al actualizar la solicitud']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

