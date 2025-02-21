<?php
session_start();
require_once '../database/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['idUser'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit();
}

$idUsuario = $_SESSION['idUser'];
$peliId = $_POST['peliId'];
$action = $_POST['action'];

try {
    if ($action === 'like') {
        // Verificar si ya existe el like primero
        $checkQuery = "SELECT COUNT(*) FROM tbl_likes WHERE id_u = :idUsuario AND id_peli = :peliId";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $checkStmt->bindParam(':peliId', $peliId, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() == 0) {
            $query = "INSERT INTO tbl_likes (id_u, id_peli) VALUES (:idUsuario, :peliId)";
        } else {
            echo json_encode(['success' => false, 'error' => 'Like ya existe']);
            exit();
        }
    } else if ($action === 'unlike') {
        $query = "DELETE FROM tbl_likes WHERE id_u = :idUsuario AND id_peli = :peliId";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->bindParam(':peliId', $peliId, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener el nuevo contador de likes
    $countQuery = "SELECT COUNT(*) as like_count FROM tbl_likes WHERE id_peli = :peliId";
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bindParam(':peliId', $peliId, PDO::PARAM_INT);
    $countStmt->execute();
    $likeCount = $countStmt->fetch(PDO::FETCH_ASSOC)['like_count'];

    echo json_encode(['success' => true, 'like_count' => $likeCount]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>