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
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #141414;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #D60404;
            padding: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        #logoCenter {
            position: relative;
            text-align: center;
        }

        #logoCenter img {
            height: 100px;
            object-fit: cover;
            margin: 0;
        }

        .user-dropdown {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 100;
        }

        .dropbtn {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
        }

        .dropdown-content {
            right: 0;
            display: none;
            position: absolute;
            background-color: #1a1a1a;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 101;
            border-radius: 4px;
            top: 100%;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #2a2a2a;
        }

        .user-dropdown:hover .dropdown-content {
            display: block;
        }

        .peliculas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .pelicula {
            background-color: #1a1a1a;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .pelicula img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 4px;
        }

        .pelicula h3 {
            color: white;
            margin: 10px 0;
        }

        .categoria-container {
            margin: 40px 20px;
        }

        .categoria-titulo-container {
            background-color: #D60404;
            padding: 10px 20px;
            position: relative;
            text-align: center;
            border-radius: 4px;
        }

        .categoria-titulo-container::before {
            content: '';
            position: absolute;
            background-color: #D60404;
            width: calc(100% - 40px);
            height: 100%;
            left: 50%;
            transform: translateX(-50%);
            top: 0;
            z-index: -1;
            border-radius: 4px;
        }

        .categoria-titulo {
            color: #000000;
            margin: 0;
            font-size: 1.5em;
            font-weight: bold;
        }

        .peliculas-grid {
            margin-top: 20px;
        }

        .filtros-container {
            padding: 20px;
            background-color: #1a1a1a;
        }

        .form-filtros {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .form-filtros select,
        .form-filtros input,
        .form-filtros button,
        .form-filtros .btn-reset {
            padding: 8px 15px;
            border-radius: 4px;
            border: 1px solid #333;
            background-color: #2a2a2a;
            color: white;
        }

        .btn-reset {
            text-decoration: none;
            background-color: #D60404;
        }

        .pelicula-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .pelicula:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }

        .destacadas-container {
    padding: 20px;
    text-align: center;
}

.peliculas-destacadas {
    display: flex;
    gap: 20px;
    overflow-x: auto;
    padding: 10px;
    justify-content: center;
}

.pelicula-card {
    background-color: #222;
    color: white;
    border-radius: 10px;
    padding: 15px;
    width: 200px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    min-height: 350px; /* Altura mínima para todas las tarjetas */
}

.pelicula-card img {
    width: 100%;
    border-radius: 10px;
    height: 250px; /* Altura fija para la imagen */
    object-fit: cover; /* Asegura que la imagen cubra el espacio sin distorsionarse */
}

.pelicula-card h3 {
    margin: 10px 0;
    font-size: 16px; /* Tamaño de fuente reducido */
    height: 50px; /* Altura fija para el título */
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden; /* Evita que el texto desborde */
    text-overflow: ellipsis; /* Agrega puntos suspensivos si el texto es demasiado largo */
    white-space: nowrap; /* Evita que el texto se divida en varias líneas */
}

.pelicula-card p {
    font-size: 12px; /* Tamaño de fuente reducido */
    color: #bbb;
}

.pelicula-card span {
    display: block;
    margin-top: 10px;
    font-weight: bold;
    font-size: 14px; /* Tamaño de fuente reducido */
}

    </style>
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

    <div class="filtros-container">
        <form action="" method="GET" class="form-filtros">
            <select name="categoria">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $categoria) : ?>
                    <option value="<?php echo $categoria['id_cat']; ?>" 
                            <?php echo (isset($_GET['categoria']) && $_GET['categoria'] == $categoria['id_cat']) ? 'selected' : ''; ?>>
                        <?php echo $categoria['nombre_cat']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="director" placeholder="Buscar por director" 
                   value="<?php echo isset($_GET['director']) ? htmlspecialchars($_GET['director']) : ''; ?>">
            
            <input type="text" name="titulo" placeholder="Buscar por título" 
                   value="<?php echo isset($_GET['titulo']) ? htmlspecialchars($_GET['titulo']) : ''; ?>">
            
            <button type="submit">Filtrar</button>
            <a href="index.php" class="btn-reset">Resetear filtros</a>
        </form>
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
        <div class="categoria-container">
            <div class="categoria-titulo-container">
                <h2 class="categoria-titulo"><?php echo htmlspecialchars($categoria); ?></h2>
            </div>
            <div class="peliculas-grid">
                <?php foreach ($peliculas as $pelicula) : ?>
                <div class="pelicula">
                    <?php if (isset($_SESSION['idUser']) && $_SESSION['idUser'] != null): ?>
                        <a href="view/detalle_pelicula.php?id=<?php echo $pelicula['id_peli']; ?>" class="pelicula-link">
                    <?php else: ?>
                        <a href="view/formRegistro.php" class="pelicula-link">
                    <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($pelicula['poster_peli']); ?>" 
                             alt="<?php echo htmlspecialchars($pelicula['titulo_peli']); ?>">
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const likeButtons = document.querySelectorAll('.like-btn');

            likeButtons.forEach(button => {
                button.addEventListener('click', function () {
                    <?php if (!isset($_SESSION['idUser'])): ?>
                        Swal.fire({
                            title: 'Error',
                            text: 'Debes iniciar sesión para dar like',
                            icon: 'error'
                        });
                        return;
                    <?php endif; ?>
                    
                    const peliId = this.getAttribute('data-peli-id');
                    const isLiked = this.classList.contains('liked');
                    const action = isLiked ? 'unlike' : 'like';
                    const likeCount = this.nextElementSibling;

                    fetch('backend/like.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `peliId=${peliId}&action=${action}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.toggle('liked');
                            likeCount.textContent = data.like_count;
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.error,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al procesar la solicitud',
                            icon: 'error'
                        });
                        console.error('Error:', error);
                    });
                });
            });
        });
    </script>
</body>
</html>