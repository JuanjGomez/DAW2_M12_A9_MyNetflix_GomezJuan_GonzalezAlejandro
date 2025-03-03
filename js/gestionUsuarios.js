document.addEventListener('DOMContentLoaded', function() {
    // Cargar usuarios inicialmente
    filtrarUsuarios();

    // Event listeners para filtros
    document.getElementById('buscador').addEventListener('input', filtrarUsuarios);
    document.getElementById('filtroEstado').addEventListener('change', filtrarUsuarios);
    document.getElementById('resetFiltros').addEventListener('click', resetearFiltros);
});

// Función para filtrar usuarios
async function filtrarUsuarios() {
    const busqueda = document.getElementById('buscador').value;
    const estado = document.getElementById('filtroEstado').value;

    try {
        const response = await fetch('../backend/fetchUsuarios.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                busqueda: busqueda,
                estado: estado
            })
        });

        const data = await response.json();
        
        if (data.success) {
            renderizarUsuarios(data.usuarios);
        } else {
            console.error('Error al filtrar usuarios:', data.error);
            alert('Error al filtrar usuarios: ' + data.error);
        }
    } catch (error) {
        console.error('Error de conexión:', error);
        alert('Error de conexión: ' + error);
    }
}

// Función para resetear filtros
function resetearFiltros() {
    document.getElementById('buscador').value = '';
    document.getElementById('filtroEstado').value = 'todos';
    filtrarUsuarios();
}

// Función para renderizar usuarios
function renderizarUsuarios(usuarios) {
    const tbody = document.getElementById('tablaUsuarios');
    if (!tbody) {
        console.error('No se encontró el elemento tablaUsuarios');
        return;
    }

    tbody.innerHTML = '';

    if (!usuarios || usuarios.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron usuarios</td></tr>';
        return;
    }

    usuarios.forEach(usuario => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="hidden">${usuario.id_u}</td>
            <td>${usuario.username_u}</td>
            <td>${usuario.email_u}</td>
            <td>${usuario.activo_u == '1' ? 'Activo' : 'Inactivo'}</td>
            <td class="hidden">${usuario.nombre_rol}</td>
            <td>
                <a href="editarUsuario.php?id=${usuario.id_u}" class="btn-edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button class="btn-delete" onclick="eliminarUsuario(${usuario.id_u})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Función para eliminar usuario
async function eliminarUsuario(id) {
    // Mostrar SweetAlert2 de confirmación
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará al usuario y todos sus registros asociados",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    // Si el usuario confirma
    if (result.isConfirmed) {
        try {
            const response = await fetch('../backend/eliminarUser.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_usuario: id
                })
            });

            const data = await response.json();

            if (data.success) {
                // Mostrar mensaje de éxito
                await Swal.fire({
                    title: '¡Eliminado!',
                    text: 'El usuario ha sido eliminado correctamente',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
                
                // Recargar la lista de usuarios
                filtrarUsuarios();
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.error || 'Error al eliminar el usuario',
                    icon: 'error'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Error',
                text: 'Error de conexión al eliminar el usuario',
                icon: 'error'
            });
        }
    }
}
