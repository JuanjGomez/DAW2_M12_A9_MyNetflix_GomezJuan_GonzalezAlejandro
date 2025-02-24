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
    if(isset($_SESSION['edicionExitosa']) && $_SESSION['edicionExitosa']){
        echo '<script>let edicionExitosa = true;</script>';
        unset($_SESSION['edicionExitosa']);
    }

    // Consulta para traer las peliculas
    try{
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
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css" integrity="sha256-qWVM38RAVYHA4W8TAlDdszO1hRaAq0ME7y2e9aab354=" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/crudPeli.css">
    <title>Document</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png">
        </div>
        <div class="user-dropdown">
            <button class="dropbtn">
                <i class="fas fa-user"></i>
            </button>
            <div class="dropdown-content">
                <a href="../backend/cerrarSesion.php">Cerrar Sesión</a>
            </div>
        </div>
    </header>
    <h1>Gestionar Peliculas</h1>
    <div class="divCentrarBotones" id="botones">
        <a href="admin.php"><button class="btn btn-danger">REGRESAR</button></a>
        <a href="formPelicula.php"><button class="btn btn-success">Añadir Pelicula</button></a>
    </div>
    <div id="navbar" class="divCentrar">
        <label for="ordenar">Ordenar por:
            <select id="ordenar">
                <option value="">Selecciona una opcion:</option>
                <option value="titulo">Nombre</option>
                <option value="likes">Numero de likes</option>
            </select>
        </label>
        <label for="categorias">Categorias: 
            <select id="categorias">
                <option value="">Selecciona una opcion:</option>
                <?php
                    $sql = "SELECT * FROM tbl_categorias";
                    $result = $conn->query($sql);
                    $categorias = $result->fetchAll(PDO::FETCH_ASSOC);
                    foreach($categorias as $categoria){
                        echo "<option value='".$categoria['id_cat']."'>".$categoria['nombre_cat']."</option>";
                    }
                ?>
            </select>
        </label>

        <label for="buscador">Buscar:
            <input type="text" id="buscador" placeholder="Buscar pelicula...">
        </label>

        <button id="resetFiltros" class="btn btn-secondary">Restablecer Filtros</button>
    </div>

    <div>
        <table border="1" id="tablaPeliculas">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Poster</th>
                    <th id="fechaEstreno">Fecha Estreno</th>
                    <th id="sinopsis">Sinopsis</th>
                    <th id="director">Director</th>
                    <th>N° Likes</th>
                    <th>Categoria(s)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Mostrar peliculas -->
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" integrity="sha256-1m4qVbsdcSU19tulVTbeQReg0BjZiW6yGffnlr/NJu4=" crossorigin="anonymous"></script>
    <script src="../js/toolsGestiPeliculas.js"></script>
</body>
</html>