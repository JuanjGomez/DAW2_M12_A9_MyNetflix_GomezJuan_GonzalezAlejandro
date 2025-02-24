<?php
session_start();
require_once '../database/conexion.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../index.php');
    exit;
}

$idPelicula = $_GET['id'];
$idUsuario = isset($_SESSION['idUser']) ? $_SESSION['idUser'] : 0;

// Obtener detalles de la película
$sql = "SELECT p.*, 
        (SELECT COUNT(*) FROM tbl_likes l WHERE l.id_peli = p.id_peli) as like_count,
        (SELECT COUNT(*) FROM tbl_likes l WHERE l.id_peli = p.id_peli AND l.id_u = :idUsuario) as user_like,
        GROUP_CONCAT(c.nombre_cat) as categorias
        FROM tbl_peliculas p 
        LEFT JOIN tbl_pelicula_categoria pc ON p.id_peli = pc.id_peli 
        LEFT JOIN tbl_categorias c ON pc.id_cat = c.id_cat 
        WHERE p.id_peli = :idPelicula
        GROUP BY p.id_peli";

$stmt = $conn->prepare($sql);
$stmt->execute([':idPelicula' => $idPelicula, ':idUsuario' => $idUsuario]);
$pelicula = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pelicula) {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pelicula['titulo_peli']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #141414;
            font-family: Arial, sans-serif;
            color: white;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .volver-btn {
            display: inline-block;
            margin-bottom: 30px;
            padding: 12px 24px;
            background-color: #e50914;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .volver-btn:hover {
            background-color: #b2070f;
        }

        .pelicula-detalle {
            display: grid;
            grid-template-columns: minmax(300px, 1fr) 2fr;
            gap: 40px;
            background-color: #1a1a1a;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .poster {
            width: 100%;
        }

        .poster img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .info {
            padding-right: 20px;
        }

        .info h1 {
            margin: 0 0 20px 0;
            font-size: 2.5em;
            color: #ffffff;
        }

        .info p {
            margin: 15px 0;
            line-height: 1.6;
            font-size: 1.1em;
        }

        .info strong {
            color: #e50914;
            font-weight: bold;
        }

        .like-container {
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .like-btn {
            background: none;
            border: none;
            color: #666;
            font-size: 1.8em;
            cursor: pointer;
            transition: color 0.3s ease;
            padding: 10px;
        }

        .like-btn:hover {
            color: #e50914;
        }

        .like-btn.liked {
            color: #e50914;
        }

        .like-count {
            font-size: 1.2em;
            color: #ffffff;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .pelicula-detalle {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .poster {
                max-width: 400px;
                margin: 0 auto;
            }

            .info {
                padding-right: 0;
            }

            .info h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../index.php" class="volver-btn">Volver al inicio</a>
        
        <div class="pelicula-detalle">
            <div class="poster">
                <img src="../<?php echo htmlspecialchars($pelicula['poster_peli']); ?>" 
                     alt="<?php echo htmlspecialchars($pelicula['titulo_peli']); ?>">
            </div>
            <div class="info">
                <h1><?php echo htmlspecialchars($pelicula['titulo_peli']); ?></h1>
                <p><strong>Director:</strong> <?php echo htmlspecialchars($pelicula['director_peli']); ?></p>
                <p><strong>Fecha de estreno:</strong> <?php echo htmlspecialchars($pelicula['fecha_estreno_peli']); ?></p>
                <p><strong>Categorías:</strong> <?php echo htmlspecialchars($pelicula['categorias']); ?></p>
                <p><strong>Descripción:</strong></p>
                <p><?php echo nl2br(htmlspecialchars($pelicula['descripcion_peli'])); ?></p>
                
                <div class="like-container">
                    <button class="like-btn <?php echo $pelicula['user_like'] ? 'liked' : ''; ?>" 
                            data-peli-id="<?php echo $pelicula['id_peli']; ?>">
                        <i class="fas fa-thumbs-up"></i>
                    </button>
                    <span class="like-count"><?php echo $pelicula['like_count']; ?></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const likeBtn = document.querySelector('.like-btn');
            
            likeBtn.addEventListener('click', function () {
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

                fetch('../backend/like.php', {
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
                    console.error('Error:', error);
                });
            });
        });
    </script>
</body>
</html> 