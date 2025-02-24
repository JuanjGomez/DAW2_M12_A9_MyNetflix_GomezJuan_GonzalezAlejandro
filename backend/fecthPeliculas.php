<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database/conexion.php';
header('Content-Type: application/json');

$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : "";
$orden = isset($_GET['orden']) && $_GET['orden'] == "likes" ? "likes" : "titulo_peli";
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : "";

// Consulta sql con filtro y orden dinámico
try{
        $sql = "SELECT p.*, GROUP_CONCAT(c.nombre_cat SEPARATOR '<br>') AS generos, COUNT(l.id_peli) AS likes_peli
                FROM tbl_peliculas p 
                LEFT JOIN tbl_pelicula_categoria pc 
                ON p.id_peli = pc.id_peli 
                LEFT JOIN tbl_categorias c 
                ON pc.id_cat = c.id_cat 
                LEFT JOIN tbl_likes l
                ON p.id_peli = l.id_peli
                WHERE p.titulo_peli LIKE :filtro 
                AND (c.id_cat = :categoria OR :categoria = '')
                GROUP BY p.id_peli 
                ORDER BY $orden ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['filtro' => "%$filtro%", 'categoria' => $categoria]);
        $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
        echo json_encode(['error' => $e->getMessage()]);
}

echo json_encode($peliculas);