<?php
session_start();
if (isset($_SESSION['successLogin']) && $_SESSION['successLogin']) {
    echo '<script>let successLogin = true</script>';
    unset($_SESSION['successLogin']);
}
if (isset($_SESSION['successCrear']) && $_SESSION['successCrear']) {
    echo '<script>let successCrear = true</script>';
    unset($_SESSION['successCrear']);
}

require_once 'database/conexion.php';

try {
    $query = "SELECT p.titulo_peli, p.poster_peli, COUNT(l.id_likes) AS total_likes 
              FROM tbl_peliculas p
              LEFT JOIN tbl_likes l ON p.id_peli = l.id_peli
              GROUP BY p.id_peli
              ORDER BY total_likes DESC
              LIMIT 5";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $peliculasDestacadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Verificar si el usuario está logueado
$idUsuario = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : 0;

// Obtener todas las categorías para el filtro
$stmt = $conn->prepare("SELECT * FROM tbl_categorias");
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="./styles/inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Document</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="img/logoN.png">
        </div>
        <div class="user-dropdown">
            <button class="dropbtn">
                <i class="fas fa-user"></i>
            </button>
            <div class="dropdown-content">
                <?php if (isset($_SESSION['idUser']) && $_SESSION['idUser'] != null) : ?>
                    <a href="./backend/cerrarSesion.php">Cerrar Sesión</a>
                <?php else : ?>
                    <a href="view/formSesion.php">Inicio Sesión</a>
                    <a href="view/formRegistro.php">Registro</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="filtros-container" <?php echo (!isset($_SESSION['idUser']) || !isset($_SESSION['actividad']) || $_SESSION['actividad'] !== 'activo') ? 'style="display: none;"' : ''; ?>>
        <div class="input-group">
            <select id="filtroCategoria" name="categoria">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $categoria) : ?>
                    <option value="<?php echo $categoria['id_cat']; ?>">
                        <?php echo $categoria['nombre_cat']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" id="filtroDirector" name="director" placeholder="Buscar por director">
            <input type="text" id="filtroTitulo" name="titulo" placeholder="Buscar por título">
            
            <!-- Nuevo filtro para likes -->
            <select id="filtroLikes" name="likes">
                <option value="">Todos los likes</option>
                <option value="mis-likes">Mis likes</option>
                <option value="sin-likes">Sin mis likes</option>
            </select>
            
            <button type="button" id="resetFiltros">Resetear filtros</button>
        </div>
    </div>

    <div class="destacadas-container">
    <h2>Películas más destacadas</h2>
    <div class="peliculas-destacadas">
        <?php foreach ($peliculasDestacadas as $pelicula): ?>
            <div class="pelicula-card">
                <h3>
                    <?php
                    $titulo = $pelicula['titulo_peli'];
                    if (mb_strlen($titulo) > 25) {
                        echo mb_substr($titulo, 0, 25) . '...'; // Limita a 28 caracteres
                    } else {
                        echo $titulo; // Muestra el título completo
                    }
                    ?>
                </h3>
                <img src="<?php echo $pelicula['poster_peli']; ?>" alt="<?php echo $pelicula['titulo_peli']; ?>">
                <span>👍 <?php echo $pelicula['total_likes']; ?> likes</span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

    <main>
        <?php foreach ($peliculasPorCategoria as $categoria => $peliculas) : ?>
        <div class="categoria-container" data-categoria-id="<?php echo htmlspecialchars($categorias[array_search($categoria, array_column($categorias, 'nombre_cat'))]['id_cat']); ?>">
            <div class="categoria-titulo-container">
                <h2 class="categoria-titulo"><?php echo htmlspecialchars($categoria); ?></h2>
            </div>
            <div class="peliculas-grid">
                <?php foreach ($peliculas as $pelicula) : ?>
                <div class="pelicula" 
                     data-director="<?php echo htmlspecialchars($pelicula['director_peli']); ?>"
                     data-titulo="<?php echo htmlspecialchars($pelicula['titulo_peli']); ?>">
                    <a href="view/detalle_pelicula.php?id=<?php echo $pelicula['id_peli']; ?>" class="pelicula-link">
                        <img src="<?php echo htmlspecialchars($pelicula['poster_peli']); ?>" alt="<?php echo htmlspecialchars($pelicula['titulo_peli']); ?>">
                        <h3>
                    <?php
                    $titulo = $pelicula['titulo_peli'];
                    if (mb_strlen($titulo) > 15) {
                        echo mb_substr($titulo, 0, 15) . '...'; // Limita a 28 caracteres
                    } else {
                        echo $titulo; // Muestra el título completo
                    }
                    ?>
                </h3>
                    </a>
                    <div class="like-container">
                        <button class="like-btn <?php echo $pelicula['user_like'] ? 'liked' : ''; ?>" 
                                data-peli-id="<?php echo $pelicula['id_peli']; ?>">
                            <i class="fas fa-thumbs-up"></i>
                        </button>
                        <span class="like-count"><?php echo $pelicula['like_count']; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="js/toolsInicio.js"></script>
</body>
</html>