<?php
session_start();
require_once '../database/conexion.php';

header('Content-Type: application/json');

// Verificar si el usuario está logueado
$idUsuario = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : 0;

try {
    // Preparar la consulta base
    $sql = "SELECT DISTINCT p.*, c.nombre_cat, 
            (SELECT COUNT(*) FROM tbl_likes l WHERE l.id_peli = p.id_peli) as like_count,
            (SELECT COUNT(*) FROM tbl_likes l WHERE l.id_peli = p.id_peli AND l.id_u = :idUsuario) as user_like
            FROM tbl_peliculas p 
            INNER JOIN tbl_pelicula_categoria pc ON p.id_peli = pc.id_peli 
            INNER JOIN tbl_categorias c ON pc.id_cat = c.id_cat 
            WHERE 1=1";
    
    $params = [':idUsuario' => $idUsuario];

    // Aplicar filtros si existen
    if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
        $sql .= " AND c.id_cat = :categoria";
        $params[':categoria'] = $_GET['categoria'];
    }

    if (isset($_GET['director']) && !empty($_GET['director'])) {
        $sql .= " AND p.director_peli LIKE :director";
        $params[':director'] = '%' . $_GET['director'] . '%';
    }

    if (isset($_GET['titulo']) && !empty($_GET['titulo'])) {
        $sql .= " AND p.titulo_peli LIKE :titulo";
        $params[':titulo'] = '%' . $_GET['titulo'] . '%';
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar películas por categoría
    $peliculasPorCategoria = array();
    foreach ($peliculas as $pelicula) {
        $peliculasPorCategoria[$pelicula['nombre_cat']][] = $pelicula;
    }

    echo json_encode([
        'success' => true,
        'peliculas' => $peliculasPorCategoria
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}