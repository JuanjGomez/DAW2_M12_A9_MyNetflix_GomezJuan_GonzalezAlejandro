<?php
session_start();
if (!isset($_SESSION['idUser']) || !in_array($_SESSION['rol'], ['administrador'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../database/conexion.php';

$id_u = $_GET['id'] ?? null;
if (!$id_u) {
    header('Location: ../view/gestionarUsuarios.php');
    exit();
}

// Obtener los datos del usuario
$sql = "SELECT * FROM tbl_usuarios WHERE id_u = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_u]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener la lista de roles
$sql_roles = "SELECT * FROM tbl_roles";
$result_roles = $conn->query($sql_roles);
?>

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
            <h1>Editar Usuario</h1>
            <form method="POST" action="../backend/procesar_edicion.php">
                <input type="hidden" name="id_u" value="<?php echo htmlspecialchars($id_u); ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($usuario['username_u']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email_u']); ?>" required>
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
                    <input type="checkbox" id="activo" name="activo" <?php echo $usuario['activo_u'] ? 'checked' : ''; ?>>
                </div>
                <div class="mb-3">
                    <label for="id_rol" class="form-label">Rol</label>
                    <select class="form-control" id="id_rol" name="id_rol" required>
                        <?php while ($rol = $result_roles->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $rol['id_rol']; ?>" <?php echo $rol['id_rol'] == $usuario['id_rol'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($rol['nombre_rol']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="actualizar_usuario" class="btn btn-primary">Actualizar Usuario</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <?php if(isset($_SESSION['error'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $_SESSION['error']; ?>',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                <?php unset($_SESSION['error']); ?>
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
