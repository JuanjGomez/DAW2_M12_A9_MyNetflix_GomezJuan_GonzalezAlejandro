document.addEventListener('DOMContentLoaded', function() {
    const tablaSolicitudes = document.getElementById('tablaSolicitudes');
    const buscador = document.getElementById('buscador');
    const resetFiltros = document.getElementById('resetFiltros');

    // Función para cargar las solicitudes
    function cargarSolicitudes(filtro = '') {
        fetch(`../backend/fetchSolicitudes.php?filtro=${filtro}`)
            .then(response => response.json())
            .then(usuarios => {
                tablaSolicitudes.innerHTML = '';
                if (usuarios.length === 0) {
                    tablaSolicitudes.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center;">No hay solicitudes pendientes.</td>
                        </tr>`;
                    return;
                }

                usuarios.forEach(usuario => {
                    const fila = `
                        <tr id="usuario-${usuario.id_u}">
                            <td>${usuario.id_u}</td>
                            <td>${usuario.username_u}</td>
                            <td>${usuario.email_u}</td>
                            <td class="hidden">${usuario.nombre_rol}</td>
                            <td class="hidden">${usuario.activo_u ? 'Activo' : 'No Activo'}</td>
                            <td>
                                <button class="btn btn-success aceptar-btn" data-id="${usuario.id_u}">Aceptar</button><br><br>
                                <button class="btn btn-danger rechazar-btn" data-id="${usuario.id_u}" data-solicitud-id="${usuario.id_soli}">Rechazar</button>
                            </td>
                        </tr>`;
                    tablaSolicitudes.innerHTML += fila;
                });
                
                // Volver a añadir los event listeners a los botones
                configurarBotonesAccion();
            })
            .catch(error => console.error('Error:', error));
    }

    // Función para configurar los event listeners de los botones
    function configurarBotonesAccion() {
        document.querySelectorAll('.aceptar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idUsuario = this.getAttribute('data-id');
                fetch('../backend/proc_solicitud.php', {
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
                        filaUsuario.remove();
                    } else {
                        alert('Error al aceptar el usuario: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        document.querySelectorAll('.rechazar-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const idUsuario = this.getAttribute('data-id');
                const idSolicitud = this.getAttribute('data-solicitud-id');
                fetch('../backend/rechazar_solicitud.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${idUsuario}&solicitud_id=${idSolicitud}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const filaUsuario = document.getElementById(`usuario-${idUsuario}`);
                        filaUsuario.remove();
                    } else {
                        alert('Error al rechazar el usuario: ' + data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    }

    // Event listener para el buscador
    let timeoutId;
    buscador.addEventListener('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            cargarSolicitudes(this.value);
        }, 300);
    });

    // Event listener para el botón de reset
    resetFiltros.addEventListener('click', function() {
        buscador.value = '';
        cargarSolicitudes();
    });

    // Cargar solicitudes iniciales
    cargarSolicitudes();
});