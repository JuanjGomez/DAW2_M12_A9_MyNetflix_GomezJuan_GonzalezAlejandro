<?php
    session_start();
    if(!isset($_SESSION['idUser'])){
        header('Location: ../index.php');
        exit();
    }
    require_once '../database/conexion.php';

    // Esto es para los sweetAlerts
    if(isset($_SESSION['errorTituloDuplicado'])){
        echo '<script>let errorTituloDuplicado = true;</script>';
        unset($_SESSION['errorTituloDuplicado']);
    }
    if(isset($_SESSION['errorImagenTipo'])){
        echo '<script>let errorImagenTipo = true;</script>';
        unset($_SESSION['errorImagenTipo']);
    }
    if(isset($_SESSION['errorImagenTamano'])){
        echo '<script>let errorImagenTamano = true;</script>';
        unset($_SESSION['errorImagenTamano']);
    }
    if(isset($_SESSION['errorImagenSubida'])){
        echo '<script>let errorImagenSubida = true;</script>';
        unset($_SESSION['errorImagenSubida']);
    }

    // Consulta para traer todas las categorias de la base de datos
    try{
        $sqlCategorias = "SELECT * 
                        FROM tbl_categorias";
        $stmtCategorias = $conn->prepare($sqlCategorias);
        $stmtCategorias->execute();
        $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

        // Consulta para traer datos de la pelicula por id
        if(isset($_POST['idPeli'])){
            $idPelicula = $_POST['idPeli'];
            $sqlPelicula = "SELECT p.id_peli, p.titulo_peli, p.poster_peli, p.fecha_estreno_peli, p.descripcion_peli, 
                                p.director_peli, GROUP_CONCAT(c.id_cat) AS id_categorias, 
                                GROUP_CONCAT(c.nombre_cat SEPARATOR ', ') AS categorias
                            FROM tbl_peliculas p
                            LEFT JOIN tbl_pelicula_categoria pc ON p.id_peli = pc.id_peli
                            LEFT JOIN tbl_categorias c ON pc.id_cat = c.id_cat
                            WHERE p.id_peli = :id
                            GROUP BY p.id_peli";
            $stmtPelicula = $conn->prepare($sqlPelicula);
            $stmtPelicula->bindParam(':id', $idPelicula, PDO::PARAM_INT);
            $stmtPelicula->execute();
            $pelicula = $stmtPelicula->fetch(PDO::FETCH_ASSOC);
        } else {
            $pelicula = [];
        }
    } catch(PDOException $e){
        echo "Error: ". $e->getMessage();
        die();
    }

    // Convertimos en array las categorias de la pelicula si existen
    $categoriasSeleccionadas = isset($pelicula['id_categorias']) ? explode(",", $pelicula['id_categorias']) : [];

    // Si hay un error previo, usamos las categorías guardadas en la sesión en lugar de las de la película
    if(isset($_SESSION['categoriasDuplicadas'])){
        $categoriasSeleccionadas = $_SESSION['categoriasDuplicadas'];
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css" integrity="sha256-ugbaEitpVSMgCpnPe7m69OyL6M47KkfE36OdRjQXD28=" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/forms.css">
    <title><?= isset($pelicula['id_peli']) ? 'Editar Película' : 'Nueva Película' ?></title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png">
        </div>
    </header>
    <a href="gestionarPeliculas.php"><button class="btn btn-danger">VOLVER</button></a>
    <h1><?= isset($pelicula['id_peli']) ? 'EDITAR PELÍCULA' : 'NUEVA PELÍCULA' ?></h1>
    <div id="centrarDiv">
        <div id="formRegistro">
            <form method="POST" action="../backend/<?= !empty($pelicula['id_peli']) ? 'editarPeli.php' : 'insertPelicula.php' ?>" enctype="multipart/form-data">
                <input type="hidden" name="idPeli" value="<?= $pelicula['id_peli'] ?? '' ?>">

                <div class="form-container">
                    <div class="form-colum">
                        <label for="titulo">Titulo: <br>
                            <input type="text" id="titulo" name="titulo" value="<?= isset($pelicula['titulo_peli']) ? $pelicula['titulo_peli'] : ($_SESSION['tituloDuplicado'] ?? ''); ?>">
                        </label>
                        <span class="error" id="errorTitulo"></span>

                        <label for="descripcion">Descripcion: <br>
                            <textarea id="descripcion" name="descripcion"><?= isset($pelicula['descripcion_peli']) ? $pelicula['descripcion_peli'] : ($_SESSION['descripcionDuplicado'] ?? ''); ?></textarea>
                        </label>
                        <span class="error" id="errorDescripcion"></span>

                        <label for="fechaEstreno">Fecha de estreno: <br>
                            <input type="date" id="fechaEstreno" name="fechaEstreno" value="<?= isset($pelicula['fecha_estreno_peli']) ? $pelicula['fecha_estreno_peli'] : ($_SESSION['fechaEstrenoDuplicado'] ?? ''); ?>">
                        </label>
                        <span class="error" id="errorFechaEstreno"></span>

                        <label for="director">Director: <br>
                            <input text="text" id="director" name="director" value="<?= isset($pelicula['director_peli']) ? $pelicula['director_peli'] : ($_SESSION['directorDuplicado'] ?? ''); ?>">
                        </label>
                        <span class="error" id="errorDirector"></span>

                        <label for="poster">Poster:
                            <input type="file" id="poster" name="poster" accept="image/jpeg, image/png, image/jpg">
                        </label>
                        <span class="error" id="errorPoster"></span>
                    </div>
                    <div class="form-colum">
                        <fieldset>
                            <legend>Categorías:</legend>
                            <?php foreach($categorias as $categoria): ?>
                                <label class="categoriaCheck">
                                <input type="checkbox" id="cat_<?= $categoria['id_cat'];?>" name="categorias[]" 
                                    value="<?= $categoria['id_cat']; ?>" 
                                    <?= in_array($categoria['id_cat'], $categoriasSeleccionadas) ? 'checked' : '' ?>>
                                <span><?= $categoria['nombre_cat']; ?></span> <!-- Nombre al lado del checkbox -->
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                        <span class="error" id="errorCategorias"></span>
                    </div>
                </div>
                <div id="btn-submit">
                    <button type="submit" id="btn-sesion" disabled>Subir</button>
                </div>
            </form>
        </div>
    </div>
    <?php
        unset($_SESSION['tituloDuplicado'], $_SESSION['descripcionDuplicada'], $_SESSION['fechaEstrenoDuplicada'], $_SESSION['directorDuplicado'], $_SESSION['categoriasDuplicadas']);
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="../js/comprobarFormPeli.js"></script>
</body>
</html>