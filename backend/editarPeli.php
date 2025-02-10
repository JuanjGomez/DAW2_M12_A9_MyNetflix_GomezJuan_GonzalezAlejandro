<?php
    session_start();
    require_once '../database/conexion.php';

    if(!isset($_SESSION['idUser']) || !in_array($_SESSION['rol'], ['administrador'])){
        header('Location:../index.php');
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location:../view/gestionarPeliculas.php');
        exit();
    }

    $idPeli = $_POST['idPeli'];
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $fechaEstreno = trim($_POST['fechaEstreno']);
    $director = trim($_POST['director']);
    $categorias = $_POST['categorias'] ?? [];

    try{
        $conn->beginTransaction();

        // Actualizar los datos de la pelicula 
        $sqlActualizarPelicula = "UPDATE tbl_peliculas 
                                SET titulo_peli = ?, descripcion_peli = ?, fecha_estreno_peli = ?, director_peli =  ?";
        if(isset($_FILES['poster']) && $_FILES['poster']['error'] == 0){
            $poster = $_FILES['poster'];
            $tiposPermitidos = ['image/png', 'image/jpeg', 'image/jpg'];
            $tamanoMax = 10 * 1024 * 1024;

            // Validar tipo de archivo
            if(!in_array($poster['type'], $tiposPermitidos)){
                $_SESSION['errorImagenTipo'] = true;
                echo "<form name='formErrorImagenTipo' method='POST' action='../view/formPelicula.php'>
                        <input type='hidden' name='idPeli' value='$idPeli'>
                    </form>";
                echo "<script> document.formErrorImagenTipo.submit() </script>";
            }

            // Validar el tamano del archivo 
            if($poster['size'] > $tamanoMax){
                $_SESSION['errorImagenTamano'] = true;
                echo "<form name='formErrorImagenTamano' method='POST' action='../view/formPelicula.php'>
                        <input type='hidden' name='idPeli' value='$idPeli'>
                    </form>";
                echo "<script> document.formErrorImagenTamano.submit() </script>";
            }

            // Ruta para guardar la imagen 
            $uploadDir = '../img/';
            $posterNom = basename($poster['name']);
            $uploadPath = $uploadDir. $posterNom;// Ruta completa para guardar la imagen

            // Mover el archivo subido a la carpeta img
            if(move_uploaded_file($poster['tmp_name'], $uploadPath)){
                // Guardar la ruta relativa para la base de datos 
                $rutaRelativaPoster = 'img/'. $posterNom;
            } else {
                $_SESSION['errorImagenSubida'] = true;
                echo "<form name='formErrorImagenSubida' method='POST' action='../view/formPelicula.php'>
                        <input type='hidden' name='idPeli' value='$idPeli'>
                    </form>";
                echo "<script> document.formErrorImagenSubida.submit() </script>";
            }

            
        } else {
            $_SESSION['errorImagenSubida'] = true;
            echo "<form name='formErrorImagenSubida' method='POST' action='../view/formPelicula.php'>
                    <input type='hidden' name='idPeli' value='$idPeli'>
                </form>";
        }


    }catch(PDOException $e){
        $conn->rollBack();
        echo "Erro" . $e->getMessage();
        die();
    }