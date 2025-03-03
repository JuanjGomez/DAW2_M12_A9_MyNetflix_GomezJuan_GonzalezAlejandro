<?php
session_start();
if (!isset($_SESSION['idUser']) || !in_array($_SESSION['rol'], ['administrador'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../database/conexion.php';

// Obtener la lista de roles
$sql_roles = "SELECT * FROM tbl_roles";
$result_roles = $conn->query($sql_roles);

?>
<?php if(isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonColor: '#d33'
            });
        <?php unset($_SESSION['error']); ?>
    </script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/formUsuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <title>Editar Usuario</title>
</head>
<body>
    <header>
        <div id="logoCenter">
            <img src="../img/logoN.png">
        </div>
    </header>
    <a href="gestionarUsuarios.php" class="btn btn-danger">Volver</a>
    <div class="container">
        <div id="formRegistro">
            <h1>Crear Usuario</h1>
            <form method="POST" action="../backend/crearUsuario.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" >
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="password2" class="form-label">Repetir Password</label>
                    <input type="password" class="form-control" id="password2" name="password2">
                </div>
                <div class="mb-3">
                    <label for="activo" class="form-label">Activo</label>
                    <input type="checkbox" id="activo" name="activo">
                </div>
                <div class="mb-3">
                    <label for="id_rol" class="form-label">Rol</label>
                    <select class="form-control" id="id_rol" name="id_rol">
                        <option selected disabled>Selecciona un rol</option>
                        <?php while ($rol = $result_roles->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $rol['id_rol']; ?>"><?php echo htmlspecialchars($rol['nombre_rol']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="actualizar_usuario" class="btn btn-primary">Crear Usuario</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
