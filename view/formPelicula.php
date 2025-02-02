<?php
    session_start();
    if(!isset($_SESSION['idUser'])){
        header('Location: ../index.php');
        exit();
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
    <div>
        <form method="POST" action="../backend/insertPelicula.php" enctype="multipart/form-data">
            <label for="titulo">Titulo: 
                <input type="text" id="titulo" name="titulo" value="<?php echo $_SESSION['tituloDuplicado'] ?? ''; ?>">
            </label>
            <span class="error" id="errorTitulo"></span>
            <label for="descripcion">Descripcion:
                <input type="text" id="descripcion" name="descripcion">
            </label>
            <span class="error" id="errorDescripcion"></span>
            <label for="fechaEstreno">Fecha de estreno:
                <input type="date" id="fechaEstreno" name="fechaEstreno">
            </label>
            <span class="error" id="errorFechaEstreno"></span>
            <label for="director">Director:
                <input text="director" id="director" name="director">
            </label>
            <span class="error" id="errorDirector"></span>
            <label for="poster">Poster:
                <input type="file" id="poster" name="poster" accept="image/jpeg, image/png, image/jpg">
            </label>
            <span class="error" id="errorPoster"></span>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.10/dist/sweetalert2.all.min.js" integrity="sha256-A9eg62yvWE5VANz+IGxBVsR7N9EWZmRsRwaGdR96vAc=" crossorigin="anonymous"></script>
</body>
</html>