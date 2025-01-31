<?php
    $user = 'root';
    $pwd = '';
    $db = 'bd_streaming';
    $host = 'localhost';

    // Conexion a la base de datos
    try{
        $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pwd);
    }catch(PDOException $e){
        echo "Error: ". $e->getMessage();
        die();
    }