<?php
    session_start();
    if(!isset($_SESSION['idUser'])){
        header('Location:../index.php');
        exit();
    }
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location:../view/formPelicula.php');
        die();
    }

    require_once '../database/conexion.php';

    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $director = trim($_POST['director']);
    $fechaEstreno = trim($_POST['fechaEstreno']);
    $categorias = $_POST['categorias'];

    if(isset($_FILES['poster']) && $_FILES['poster']['error'] == 0){
        $poster = $_FILES['poster'];
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg'];
        $tamanoMax = 10 * 1024 * 1024;// 10MB

        //Validar tipo de archivo
        if(!in_array($poster['type'], $tiposPermitidos)){
            $_SESSION['errorImagenTipo'] = true;
            header('Location: ../view/formPelicula.php');
            exit();
        }

        // Validar el tamano del archivo
        if($poster['size'] > $tamanoMax){
            $_SESSION['errorImagenTamano'] = true;
            header('Location:../view/formPelicula.php');
            exit();
        }

        // Ruta para guardar la imagen
        $uploadDir = '../img/';
        $posterNom = basename($poster['name']);
        $uploadPath = $uploadDir . $posterNom;// Ruta completa para guardar la imagen

        // Mover el archivo subido a la carpeta img
        if(move_uploaded_file($poster['tmp_name'], $uploadPath)){
            // Guardar la ruta relativa para la base de datos
            $rutaRelativaPoster = 'img/' . $posterNom;
        } else {
            $_SESSION['errorImagenSubida'] = true;
            header('Location:../view/formPelicula.php');
            exit();
        }

    } else {
        $_SESSION['errorImagen'] = true;
        header('Location:../view/formPelicula.php');
        exit();
    }

    try{
        $conn->beginTransaction();

        // Verificar que haya duplicados por titulo
        $sqlDuplicadoPelicula = "SELECT * 
                                    FROM tbl_peliculas 
                                    WHERE titulo_peli = :titulo";
        $stmtDuplicadoPelicula = $conn->prepare($sqlDuplicadoPelicula);
        $stmtDuplicadoPelicula->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmtDuplicadoPelicula->execute();
        $peliculaDuplicada = $stmtDuplicadoPelicula->fetch(PDO::FETCH_ASSOC);

        if($peliculaDuplicada){
            $_SESSION['tituloDuplicado'] = $titulo;
            $_SESSION['descripcionDuplicado'] = $descripcion;
            $_SESSION['fechaEstrenoDuplicado'] = $fechaEstreno;
            $_SESSION['directorDuplicado'] = $director;
            $_SESSION['categoriasDuplicadas'] = $categorias;
            $_SESSION['errorTituloDuplicado'] = true;
            header('Location:../view/formPelicula.php');
            $conn->rollBack();
            exit();
        }

        // Insert de la pelicula 
        $sqlInsertPelicula = "INSERT INTO tbl_peliculas (titulo_peli, descripcion_peli, poster_peli, fecha_estreno_peli, director_peli) 
                                VALUES (:titulo, :descripcion, :poster, :fecha_estreno, :director)";
        $stmtInsertPelicula = $conn->prepare($sqlInsertPelicula);
        $stmtInsertPelicula->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmtInsertPelicula->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmtInsertPelicula->bindParam(':poster', $rutaRelativaPoster, PDO::PARAM_STR);
        $stmtInsertPelicula->bindParam(':fecha_estreno', $fechaEstreno, PDO::PARAM_STR);
        $stmtInsertPelicula->bindParam(':director', $director, PDO::PARAM_STR);
        $stmtInsertPelicula->execute();

        // Obtener el id de la pelicula insertada
        $idPelicula = $conn->lastInsertId();

        // Insert de las categorias seleccionadas
        foreach($categorias as $categoria){
            $sqlInsertCategoriaPeli = "INSERT INTO tbl_pelicula_categoria (id_peli, id_cat) 
                                        VALUES (:idPeli, :idCategoria)";
            $stmtInsertCategoriaPeli = $conn->prepare($sqlInsertCategoriaPeli);
            $stmtInsertCategoriaPeli->bindParam(':idPeli', $idPelicula, PDO::PARAM_INT);
            $stmtInsertCategoriaPeli->bindParam(':idCategoria', $categoria, PDO::PARAM_INT);
            $stmtInsertCategoriaPeli->execute();
        }

        $conn->commit();

        $_SESSION['peliculaCreada'] = true;
        header('Location:../view/gestionarPeliculas.php');
        exit();
    } catch(PDOException $e){
        $conn->rollBack();
        echo "Error: ". $e->getMessage();
        die();
    }