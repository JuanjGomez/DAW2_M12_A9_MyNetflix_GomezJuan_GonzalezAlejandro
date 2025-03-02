<?php
session_start();


require_once '../database/conexion.php';
// Configuración de conexión a la base de datos

// Verificación de sesión y permisos
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



// Obtener lista de usuarios
$sql = "SELECT u.id_u, u.username_u, u.email_u, u.activo_u, r.nombre_rol 
        FROM tbl_usuarios u 
        JOIN tbl_roles r ON u.id_rol = r.id_rol";
$stmt = $conn->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de roles
$sql_roles = "SELECT * FROM tbl_roles";
$stmt_roles = $conn->query($sql_roles);
$roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);
?>

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
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal">Crear Nuevo Usuario</button>
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

    <!-- Modal para crear nuevo usuario -->
    <div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../backend/crearUsuario.php">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="activo" class="form-label">Activo</label>
                            <input type="checkbox" id="activo" name="activo">
                        </div>
                        <div class="mb-3">
                            <label for="id_rol" class="form-label">Rol</label>
                            <select class="form-control" id="id_rol" name="id_rol" required>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol['id_rol']; ?>"><?php echo $rol['nombre_rol']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="crear_usuario" class="btn btn-primary">Crear Usuario</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../js/gestionUsuarios.js"></script>
</body>
</html>