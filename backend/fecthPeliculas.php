<?php
// Asegurarnos de que no haya salida antes del JSON
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require_once '../database/conexion.php';

$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : "";
$orden = isset($_GET['orden']) ? trim($_GET['orden']) : "titulo";
$categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : "";

try {
        // Construir la consulta SQL de manera segura
        $sql = "SELECT p.*, 
                GROUP_CONCAT(DISTINCT c.nombre_cat SEPARATOR ', ') AS generos, 
                COUNT(DISTINCT l.id_likes) AS likes_peli
                FROM tbl_peliculas p 
                LEFT JOIN tbl_pelicula_categoria pc ON p.id_peli = pc.id_peli 
                LEFT JOIN tbl_categorias c ON pc.id_cat = c.id_cat 
                LEFT JOIN tbl_likes l ON p.id_peli = l.id_peli
                WHERE p.titulo_peli LIKE :filtro ";
        if (!empty($categoria)) {
                $sql .= " AND (pc.id_cat = :categoria)";
        }

        $sql .= " GROUP BY p.id_peli ";

        // Determinar el orden
        switch($orden) {
                case 'likes':
                        $sql .= " ORDER BY likes_peli DESC";
                        break;
                case 'titulo':
                        $sql .= " ORDER BY p.titulo_peli ASC";
                        break;
                default:
                        $sql .= " ORDER BY p.titulo_peli ASC";
                        break;
        }

        $stmt = $conn->prepare($sql);

        // Bind de parámetros
        $params = ['filtro' => "%$filtro%"];
        if (!empty($categoria)) {
                $params['categoria'] = $categoria;
        }

        $stmt->execute($params);
        $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Asegurarnos de que la respuesta sea JSON válido
        echo json_encode($peliculas, JSON_UNESCAPED_UNICODE);
        exit();

} catch(PDOException $e) {
        // En caso de error, devolver un JSON válido
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit();
}