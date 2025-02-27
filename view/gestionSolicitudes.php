<?php
session_start();

require_once '../database/conexion.php';

// Verificación de sesión y permisos
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}

// Obtener usuarios no activos
$query = "SELECT u.id_u, u.username_u, u.email_u, u.activo_u, r.nombre_rol, s.id_soli 
        FROM tbl_usuarios u
        INNER JOIN tbl_roles r ON u.id_rol = r.id_rol 
        INNER JOIN tbl_solicitudes_registro s ON u.id_u = s.id_u
        WHERE s.estado = 'pendiente'";
$stmt = $conn->query($query);
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/solicitudes.css"> <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <title>Solicitudes de Registro</title>
</head>
<body>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png" alt="Logo">
        </div>
        <div class="user-dropdown">
            <button class="dropbtn">
                <i class="fas fa-user"></i>
            </button>
            <div class="dropdown-content">
                <a href="../backend/cerrarSesion.php">Cerrar Sesión</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h1>Solicitudes de Registro Pendientes</h1>
        
        <!-- Añadir el div de filtros -->
        <div class="filtros-container">
            <a href="admin.php"><button class="btn btn-danger">Volver</button></a><br><br>
            <div class="input-group mb-3">
                <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre de usuario...">
                <button class="btn btn-secondary" id="resetFiltros">Restablecer</button>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Email</th>
                    <th class="hidden">Rol</th>
                    <th class="hidden">Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaSolicitudes">
                <!-- El contenido se cargará dinámicamente -->
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../js/gestionSolicitudes.js"></script>
</body>
</html>