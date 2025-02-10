<?php
    session_start();
    if(!isset($_SESSION['idUser']) || in_array($_SESSION['rol'], ['administrador'])){
        header('Location:../index.php');
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        header('Location:../view/gestionarPeliculas.php');
        exit();
    }

    require_once '../database/conexion.php';
    $idPelicula = $_POST['idPeli'];

    try{
        $conn->beginTransaction();

        // Consulta para eliminar las relaciones de la tabla likes que son la pelicula enviada por el id
        $sqlEliminarLikes = "DELETE FROM tbl_likes 
                            WHERE id_peli = ?";
        $stmtEliminarLikes = $conn->prepare($sqlEliminarLikes);
        $stmtEliminarLikes->bindParam("?", $idPelicula, PDO::PARAM_INT);
        $stmtEliminarLikes->execute();

        // Consulta para eliminar los datos de n a n en la tabla tbl_pelicula_registro de la peli enviada por el id
        $sqlEliminarPeliculaCategoria = "DELETE FROM tbl_pelicula_registro 
                                        WHERE id_peli = ?";
        $stmtEliminarPeliculaCategoria = $conn->prepare($sqlEliminarPeliculaCategoria);
        $stmtEliminarPeliculaCategoria->bindParam("?", $idPelicula, PDO::PARAM_INT);
        $stmtEliminarPeliculaCategoria->execute();

        // Consulta para eliminar la pelicula de la tabla peliculas
        $sqlBorrarPeli = "DELETE FROM tbl_peliculas 
                        WHERE id_peli = ?";
        $stmtBorrarPeli = $conn->prepare($sqlBorrarPeli);
        $stmtBorrarPeli->bindParam("?", $idPelicula, PDO::PARAM_INT);
        $stmtBorrarPeli->execute();

        $conn->commit();
        $_SESSION['deleteCorrect'] = true;
        header('Location: ../view/gestionarPeliculas.php');
        exit();
        
    }catch(PDOException $e){
        $_SESSION['errorBorrar'] = true;
        header('Location:../view/gestionarPeliculas.php');
        exit();
    }