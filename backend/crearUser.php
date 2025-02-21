<?php
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}

// Manejo de creación de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_usuario'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $activo = isset($_POST['activo']) ? 1 : 0;
    $id_rol = $_POST['id_rol'];

    $sql = "INSERT INTO tbl_usuarios (username_u, email_u, password_u, activo_u, id_rol) VALUES (:username, :email, :password, :activo, :id_rol)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);
    $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
    $stmt->execute();
}
