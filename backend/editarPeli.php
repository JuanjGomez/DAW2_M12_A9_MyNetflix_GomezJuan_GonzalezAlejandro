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
        $params = [$titulo, $descripcion, $fechaEstreno, $director];

        // Si se sube un nuevo poster
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

                // Si ya habia un poster, eliminar el antiguo
                // Primero obtenemos el poster actual de la base de datos
                $sqlPosterAntiguo = "SELECT poster_peli 
                                    FROM tbl_peliculas 
                                    WHERE id_peli = :idPeli";
                $stmtPosterAntiguo = $conn->prepare($sqlPosterAntiguo);
                $stmtPosterAntiguo->bindParam(':idPeli', $idPeli, PDO::PARAM_INT);
                $stmtPosterAntiguo->execute();
                $posterAntiguo = $stmtPosterAntiguo->fetch(PDO::FETCH_ASSOC);

                if($posterAntiguo && file_exists($posterAntiguo['poster_peli'])){
                    unlink($posterAntiguo['poster_peli']);
                }

                //Ahora anadimos el nuevo poster a la consulta
                $sqlActualizarPelicula .= ", poster_peli = ?";
                $params[] = $rutaRelativaPoster;
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
            echo "<script> document.formErrorImagenSubida.submit() </script>";
        }

        // Ejecutamos la consulta para actualizar los datos 
        $sqlActualizarPelicula .= " WHERE id_peli = ?";
        $params[] = $idPeli;
        $stmtActualizarPelicula = $conn->prepare($sqlActualizarPelicula);
        $stmtActualizarPelicula->execute($params);

        // Eliminar las categorias actuales de la pelicula
        $sqlBorrarCategoriasPeli = "DELETE FROM tbl_pelicula_categoria 
                                    WHERE id_peli = :idPeli";
        $stmtBorrarCategoriasPeli = $conn->prepare($sqlBorrarCategoriasPeli);
        $stmtBorrarCategoriasPeli->bindParam(':idPeli', $idPeli, PDO::PARAM_INT);
        $stmtBorrarCategoriasPeli->execute();

        // Insert de las nuevas categorias seleccionadas
        $sqlInsertCategoria = "INSERT INTO tbl_pelicula_categoria (id_peli, id_cat) 
                                VALUES (?, ?)";
        $stmtInsertCategoria = $conn->prepare($sqlInsertCategoria);

        foreach($categorias as $idCategoria){
            $stmtInsertCategoria->execute([$idPeli, $idCategoria]);
        }

        // Confirmar la transacción
        $conn->commit();
        $_SESSION['edicionExitosa'] = true;
        header('Location:../view/gestionarPeliculas.php');
        exit();
    }catch(PDOException $e){
        $conn->rollBack();
        echo "Erro" . $e->getMessage();
        die();
    }