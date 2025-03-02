document.addEventListener('DOMContentLoaded', function() {
    const tablaUsuarios = document.getElementById('tablaUsuarios');
    const buscador = document.getElementById('buscador');
    const filtroEstado = document.getElementById('filtroEstado');
    const resetFiltros = document.getElementById('resetFiltros');

    // Función para cargar usuarios
    function cargarUsuarios(filtro = '', estado = 'todos') {
        fetch(`../backend/fetchUsuarios.php?filtro=${encodeURIComponent(filtro)}&estado=${estado}`)
            .then(response => response.json())
            .then(usuarios => {
                tablaUsuarios.innerHTML = '';
                if (usuarios.length === 0) {
                    tablaUsuarios.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center;">No se encontraron usuarios.</td>
                        </tr>`;
                    return;
                }

                usuarios.forEach(usuario => {
                    const fila = `
                        <tr>
                            <td class="hidden">${usuario.id_u}</td>
                            <td>${usuario.username_u}</td>
                            <td>${usuario.email_u}</td>
                            <td>${usuario.activo_u ? 'Sí' : 'No'}</td>
                            <td class="hidden">${usuario.nombre_rol}</td>
                            <td>
                                <a href="editarUsuario.php?id=${usuario.id_u}" class="btn btn-warning">Editar</a><p></p>
                                <a href="../backend/eliminarUser.php?id=${usuario.id_u}" class="btn btn-danger" 
                                   onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                            </td>
                        </tr>`;
                    tablaUsuarios.innerHTML += fila;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                tablaUsuarios.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger">
                            Error al cargar los usuarios. Por favor, intente de nuevo.
                        </td>
                    </tr>`;
            });
    }

    // Event listener para el buscador con debounce
    let timeoutId;
    buscador.addEventListener('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            cargarUsuarios(this.value, filtroEstado.value);
        }, 300);
    });

    // Event listener para el filtro de estado
    filtroEstado.addEventListener('change', function() {
        cargarUsuarios(buscador.value, this.value);
    });

    // Event listener para el botón de reset
    resetFiltros.addEventListener('click', function() {
        buscador.value = '';
        filtroEstado.value = 'todos';
        cargarUsuarios();
    });

    // Cargar usuarios inicialmente
    cargarUsuarios();
});
