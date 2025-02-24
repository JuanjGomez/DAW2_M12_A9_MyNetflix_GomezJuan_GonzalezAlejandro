<?php
session_start();

require_once '../database/conexion.php';

// Verificación de sesión y permisos
if (!isset($_SESSION['idUser']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../index.php');
    exit();
}

// Obtener usuarios no activos
$query = "
    SELECT u.id_u, u.username_u, u.email_u, u.activo_u, r.nombre_rol 
    FROM tbl_usuarios u
    JOIN tbl_roles r ON u.id_rol = r.id_rol
    WHERE u.activo_u = FALSE
";
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
    </header>

    <div class="container">
        <h1>Solicitudes de Registro Pendientes</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($usuarios) > 0): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr id="usuario-<?= htmlspecialchars($usuario['id_u']) ?>">
                            <td><?= htmlspecialchars($usuario['id_u']) ?></td>
                            <td><?= htmlspecialchars($usuario['username_u']) ?></td>
                            <td><?= htmlspecialchars($usuario['email_u']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre_rol']) ?></td>
                            <td><?= $usuario['activo_u'] ? 'Activo' : 'No Activo' ?></td>
                            <td>
                                <button class="btn btn-success aceptar-btn" data-id="<?= htmlspecialchars($usuario['id_u']) ?>">Aceptar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No hay solicitudes pendientes.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aceptarBtns = document.querySelectorAll('.aceptar-btn');

            aceptarBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const idUsuario = this.getAttribute('data-id');

                    fetch('../backend/proc_solicitud.php', { // Ruta actualizada
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${idUsuario}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const filaUsuario = document.getElementById(`usuario-${idUsuario}`);
                            filaUsuario.querySelector('td:nth-child(5)').textContent = 'Activo';
                            btn.disabled = true;
                            btn.textContent = 'Aceptado';
                        } else {
                            alert('Error al aceptar el usuario: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            });
        });
    </script>
</body>
</html>