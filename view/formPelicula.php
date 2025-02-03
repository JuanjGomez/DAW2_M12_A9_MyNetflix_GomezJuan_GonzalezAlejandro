<?php
    session_start();
    if(!isset($_SESSION['idUser'])){
        header('Location: ../index.php');
        exit();
    }
    require_once '../database/conexion.php';

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
                            <input type="text" id="descripcion" name="descripcion">
                        </label>
                        <span class="error" id="errorDescripcion"></span>
                        <label for="fechaEstreno">Fecha de estreno: <br>
                            <input type="date" id="fechaEstreno" name="fechaEstreno">
                        </label>
                        <span class="error" id="errorFechaEstreno"></span>
                    </div>
                    <div class="form-colum">
                        <label for="director">Director: <br>
                            <input text="director" id="director" name="director">
                        </label>
                        <span class="error" id="errorDirector"></span>
                        <fieldset>
                            <legend>Categorias:</legend>
                                <?php foreach($categorias as $categoria):?>
                                    <label for="cateogiras">
                                        <input type="checkbox" id="<?php echo $categoria['id_cat'];?>" name="categorias[]" value="<?php echo $categoria['id_cat'];?>">
                                    <?php echo $categoria['nombre_cat'];?></label>
                                <?php endforeach;?>
                        </fieldset>
                        <label for="poster">Poster:
                            <input type="file" id="poster" name="poster" accept="image/jpeg, image/png, image/jpg">
                        </label>
                        <span class="error" id="errorPoster"></span>
                    </div>
                    <div id="btn-submit">
                        <button type="submit" id="btn-sesion" disabled>Subir</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
    <script src="../js/comprobarFormPeli.js"></script>
</body>
</html>