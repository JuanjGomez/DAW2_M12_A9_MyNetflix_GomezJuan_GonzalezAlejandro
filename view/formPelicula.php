<?php
    session_start();
    if(!isset($_SESSION['idUser'])){
        header('Location: ../index.php');
        exit();
    }
    require_once '../database/conexion.php';

    if(isset($_SESSION['errorTituloDuplicado'])){
        echo '<script>let errorTituloDuplicado = true;</script>';
        unset($_SESSION['errorTituloDuplicado']);
    }

    // Consulta para traer todas las categorias de la base de datos
    try{
        $sqlCategorias = "SELECT * 
                        FROM tbl_categorias";
        $stmtCategorias = $conn->prepare($sqlCategorias);
        $stmtCategorias->execute();
        $categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e){
        echo "Error: ". $e->getMessage();
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.min.css" integrity="sha256-ugbaEitpVSMgCpnPe7m69OyL6M47KkfE36OdRjQXD28=" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/forms.css">
    <title>Document</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png">
        </div>
    </header>
    <h1>NUEVA PELICULA</h1>
    <div id="centrarDiv">
        <div id="formRegistro">
            <form method="POST" action="../backend/insertPelicula.php" enctype="multipart/form-data">
                <div class="form-container">
                    <div class="form-colum">
                        <label for="titulo">Titulo: <br>
                            <input type="text" id="titulo" name="titulo" value="<?php echo $_SESSION['tituloDuplicado'] ?? ''; ?>">
                        </label>
                        <span class="error" id="errorTitulo"></span>
                        <label for="descripcion">Descripcion: <br>
                            <textarea id="descripcion" name="descripcion"><?php echo $_SESSION['descripcionDuplicado'] ?? ''; ?></textarea>
                        </label>
                        <span class="error" id="errorDescripcion"></span>
                        <label for="fechaEstreno">Fecha de estreno: <br>
                            <input type="date" id="fechaEstreno" name="fechaEstreno" value="<?php echo $_SESSION['fechaEstrenoDuplicado'] ?? ''; ?>">
                        </label>
                        <span class="error" id="errorFechaEstreno"></span>
                        <label for="director">Director: <br>
                            <input text="text" id="director" name="director" value="<?php echo $_SESSION['directorDuplicado'] ?? ''; ?>">
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
                                <input type="checkbox" id="cat_<?php echo $categoria['id_cat'];?>" name="categorias[]" value="<?php echo $categoria['id_cat'];?>" 
                                <?php
                                    if (isset($_SESSION['categoriasDuplicadas']) && in_array($categoria['id_cat'], $_SESSION['categoriasDuplicadas'])) {
                                        echo 'checked';
                                    }
                                ?>>
                                <span><?php echo $categoria['nombre_cat']; ?></span> <!-- Nombre al lado del checkbox -->
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