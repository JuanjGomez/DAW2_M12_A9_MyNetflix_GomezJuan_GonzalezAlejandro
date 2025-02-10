<?php
    session_start();
    if(!isset($_SESSION['idUser']) || !in_array($_SESSION['rol'], ['administrador'])){
        header('Location:../index.php');
        exit();
    }

    // Manejo de sesiones para SweetAlerts
    if(isset($_SESSION['deleteCorrect']) && $_SESSION['deleteCorrect']){
        echo '<script>let deleteCorrect = true;</script>';
        unset($_SESSION['deleteCorrect']);
    }
    if(isset($_SESSION['peliculaCreada']) && $_SESSION['peliculaCreada']){
        echo '<script>let peliculaCreada = true;</script>';
        unset($_SESSION['peliculaCreada']);
    }
    if(isset($_SESSION['editarCreada']) && $_SESSION['editarCreada']){
        echo '<script>let editarCreada = true;</script>';
        unset($_SESSION['editarCreada']);
    }

    // Consulta para traer las peliculas
    require_once '../database/conexion.php';
    $sql = "SELECT p.*, GROUP_CONCAT(c.nombre_cat SEPARATOR ', ') AS generos 
            FROM tbl_peliculas p 
            INNER JOIN tbl_pelicula_categoria pc 
            ON p.id_peli = pc.id_peli 
            INNER JOIN tbl_categorias c 
            ON pc.id_cat = c.id_cat 
            GROUP BY p.id_peli";
    $result = $conn->query($sql);
    $peliculas = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
    <h2>Gestionar Peliculas</h2>
    <a href="formPelicula.php">Añadir Pelicula</a>
    <table border="1">
        <tr>
            <th>Título</th>
            <th>Poster</th>
            <th>Fecha Estreno</th>
            <th>Director</th>
            <th>Categoria(s)</th>
            <th>Acciones</th>
        </tr>
        <?php if(isset($peliculas)): ?>
            <?php foreach($peliculas as $pelicula): ?>
                <tr>
                    <td><?= $pelicula['titulo_peli'] ?></td>
                    <td><img src="../<?= $pelicula['poster_peli'] ?>" width="100" alt="Poster"></td>
                    <td><?= $pelicula['fecha_estreno_peli']?></td>
                    <td><?= $pelicula['director_peli']?></td>
                    <td><?= $pelicula['generos'] ?></td>
                    <td>
                        <form method="POST" action="formPelicula.php">
                            <input type="hidden" name="idPeli" value="<?= $pelicula['id_peli'] ?>">
                            <button type="submit" class="btn btn-warning">Editar</button>
                        </form>
                        <form method="POST" action="../backend/deletePeli.php" id="deleteForm">
                            <input type="hidden" name="idPeli" value="<?= $pelicula['id_peli'] ?>">
                            <button type="submit" class="btn btn-danger" onclick="confirmarDelete(<?= $pelicula['id_peli'] ?>)">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr>
            <td colspan="6">No hay películas registradas</td>
            </tr>
        <?php endif;?>
    </table>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script src="../js/toolsGestiPeliculas.js"></script>
</body>
</html>