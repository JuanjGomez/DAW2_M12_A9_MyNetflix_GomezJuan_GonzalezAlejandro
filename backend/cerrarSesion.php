<?php
    session_start();
    if(!isset($_SESSION['idUser']) && $_SESSION['idUser']){
        header('Location:../index.php');
        exit();
    }

    if(isset($_SESSION['idUser']) && $_SESSION['idUser']){
        unset($_SESSION['idUser']);
    }

    if(isset($_SESSION['rol']) && $_SESSION['rol']){
        unset($_SESSION['rol']);
    }

    if(isset($_SESSION['actividad']) && $_SESSION['actividad']){
        unset($_SESSION['actividad']);
    }

    if(isset($_SESSION['username']) && $_SESSION['username']){
        unset($_SESSION['username']);
    }

    session_destroy();
    header('Location: ../index.php');
    exit();