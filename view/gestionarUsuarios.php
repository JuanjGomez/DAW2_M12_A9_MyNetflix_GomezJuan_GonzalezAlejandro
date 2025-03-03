<?php
session_start();


require_once '../database/conexion.php';
// Configuración de conexión a la base de datos

// Verificación de sesión y permisos
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}
?>
<?php if(isset($_SESSION['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonColor: '#28a745'
            });
        <?php unset($_SESSION['success']); ?>
    </script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/gestionarUsuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Gestionar Usuarios</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png">
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
        <h1>Gestionar Usuarios</h1>
        <div class="filtros-container">
            <div class="input-group mb-3">
                <select id="filtroEstado" class="form-select">
                    <option value="todos">Todos los usuarios</option>
                    <option value="activos">Usuarios activos</option>
                    <option value="inactivos">Usuarios inactivos</option>
                </select>
                <input type="text" id="buscador" class="form-control" placeholder="Buscar por nombre de usuario...">
                <button class="btn btn-secondary" id="resetFiltros">Restablecer</button>
            </div>
        </div>
        <div class="divVolver">
            <a href="admin.php"><button class="btn btn-danger">Volver</button></a>
            <a href="formCrearUser.php"><button class="btn btn-success">Crear Nuevo Usuario</button></a>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="hidden">ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Activo</th>
                    <th class="hidden">Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaUsuarios">
                <!-- El contenido se cargará dinámicamente -->
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/gestionUsuarios.js"></script>
</body>
</html>