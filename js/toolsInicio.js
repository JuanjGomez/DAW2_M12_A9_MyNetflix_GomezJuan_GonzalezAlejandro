// SweetAlerts ----------------------------------------------------------------------------------------------
// Alert para mostrar que se inicio sesion con exito
if(typeof successLogin !== 'undefined' && successLogin){
    Swal.fire({
        icon:'Bienvenido',
        title: 'Has iniciado sesión correctamente!',
        icon: 'success',
        confirmButtonText: 'Aceptar'
    });
}
// Alert para mostrar que se creo el usuario y a espera
if(typeof successCrear !== 'undefined' && successCrear){
    Swal.fire({
        title: 'Has creado tu cuenta correctamente!',
        text: 'Ah spera hasta que el administrador confirme',
        icon: 'success',
        confirmButtonText: 'Aceptar'
    })
}
// ----------------------------------------------------------------------------------------------------------

// Fetch para likes y filtros -------------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    // Variables para los filtros
    const filtrosContainer = document.querySelector('.filtros-container');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const filtroDirector = document.getElementById('filtroDirector');
    const filtroTitulo = document.getElementById('filtroTitulo');
    const resetFiltros = document.getElementById('resetFiltros');
    const categoriasContainers = document.querySelectorAll('.categoria-container');
    const filtroLikes = document.getElementById('filtroLikes');
    

    // Función para crear el delay en las búsquedas
    let timeoutId;
    function debounce(func, delay) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(func, delay);
    }

    // Función para filtrar las películas
    function filtrarPeliculas() {
        const categoriaValue = filtroCategoria.value.toLowerCase();
        const directorValue = filtroDirector.value.toLowerCase();
        const tituloValue = filtroTitulo.value.toLowerCase();
        const likesValue = filtroLikes.value;

        console.log('Filtrando con:', { categoriaValue, directorValue, tituloValue, likesValue }); // Debug

        categoriasContainers.forEach(categoriaContainer => {
            let shouldShowCategoria = false;
            const peliculas = categoriaContainer.querySelectorAll('.pelicula');
            
            // Si hay un filtro de categoría seleccionado
            if (categoriaValue) {
                const categoriaId = categoriaContainer.getAttribute('data-categoria-id');
                if (categoriaId !== categoriaValue) {
                    categoriaContainer.style.display = 'none';
                    return;
                }
            }

            peliculas.forEach(pelicula => {
                const titulo = pelicula.querySelector('h3').textContent.toLowerCase();
                const director = pelicula.getAttribute('data-director')?.toLowerCase() || '';
                const likeButton = pelicula.querySelector('.like-btn');
                const tieneMyLike = likeButton.classList.contains('liked');
                
                let cumpleFiltroLikes = true;
                if (likesValue === 'mis-likes') {
                    cumpleFiltroLikes = tieneMyLike;
                } else if (likesValue === 'sin-likes') {
                    cumpleFiltroLikes = !tieneMyLike;
                }

                const cumpleFiltros = 
                    (!tituloValue || titulo.includes(tituloValue)) &&
                    (!directorValue || director.includes(directorValue)) &&
                    cumpleFiltroLikes;

                if (cumpleFiltros) {
                    pelicula.style.display = '';
                    shouldShowCategoria = true;
                } else {
                    pelicula.style.display = 'none';
                }
            });

            // Mostrar/ocultar la categoría completa
            categoriaContainer.style.display = shouldShowCategoria ? '' : 'none';
        });
    }

    // Función para inicializar los filtros
    function inicializarFiltros() {
        console.log('Inicializando filtros...'); // Debug

        // Event listeners para los filtros
        filtroCategoria.addEventListener('change', () => {
            console.log('Cambio en categoría:', filtroCategoria.value); // Debug
            filtrarPeliculas();
        });

        filtroDirector.addEventListener('input', () => {
            console.log('Cambio en director:', filtroDirector.value); // Debug
            debounce(filtrarPeliculas, 300);
        });

        filtroTitulo.addEventListener('input', () => {
            console.log('Cambio en título:', filtroTitulo.value); // Debug
            debounce(filtrarPeliculas, 300);
        });

        // Añadir event listener para el filtro de likes
        filtroLikes.addEventListener('change', () => {
            console.log('Cambio en filtro de likes:', filtroLikes.value);
            filtrarPeliculas();
        });

        // Reset de filtros
        resetFiltros.addEventListener('click', () => {
            console.log('Reseteando filtros...');
            filtroCategoria.value = '';
            filtroDirector.value = '';
            filtroTitulo.value = '';
            filtroLikes.value = '';
            
            categoriasContainers.forEach(container => {
                container.style.display = '';
                const peliculas = container.querySelectorAll('.pelicula');
                peliculas.forEach(pelicula => {
                    pelicula.style.display = '';
                });
            });
        });
    }

    // Verificar permisos de filtrado
    async function verificarPermisosFiltros() {
        try {
            const response = await fetch('backend/checkFiltrosPermisiones.php');
            const data = await response.json();
            
            console.log('Respuesta del servidor:', data);

            if (data.hasPermission) {
                console.log('Usuario tiene permisos - Mostrando filtros');
                filtrosContainer.style.display = 'block';
                inicializarFiltros();
            } else {
                console.log('Usuario sin permisos - Ocultando filtros');
                filtrosContainer.style.display = 'none';
            }
        } catch (error) {
            console.error('Error al verificar permisos:', error);
            filtrosContainer.style.display = 'none';
        }
    }

    // Inicializar todo
    console.log('Iniciando verificación de permisos...');
    verificarPermisosFiltros();

    // Función para inicializar los botones de like
    function initializeLikeButtons() {
        const likeButtons = document.querySelectorAll('.like-btn');
        
        likeButtons.forEach(button => {
            button.addEventListener('click', async function () {
                try {
                    const sessionResponse = await fetch('backend/checkSession.php');
                    const sessionData = await sessionResponse.json();
                    
                    console.log('Datos de sesión:', sessionData);

                    if (!sessionData.isLoggedIn) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Debes iniciar sesión para dar like',
                            icon: 'error'
                        });
                        return;
                    }

                    // Si la cuenta está activa y la última solicitud es 'aprobado', permitir like
                    if (sessionData.activo === '1' && 
                        (sessionData.estadoSolicitud === 'aprobado' || sessionData.estadoSolicitud === 'none')) {
                        // Proceder con el like
                        const peliId = this.getAttribute('data-peli-id');
                        const isLiked = this.classList.contains('liked');
                        const action = isLiked ? 'unlike' : 'like';
                        const likeCount = this.nextElementSibling;

                        const response = await fetch('backend/like.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `peliId=${peliId}&action=${action}`
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            this.classList.toggle('liked');
                            likeCount.textContent = data.like_count;
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.error || 'Error al procesar la solicitud',
                                icon: 'error'
                            });
                        }
                        return;
                    }

                    // Si hay una solicitud pendiente
                    if (sessionData.estadoSolicitud === 'pendiente') {
                        Swal.fire({
                            title: 'Solicitud en Revisión',
                            text: 'Tu solicitud está en proceso de revisión.',
                            icon: 'info'
                        });
                        return;
                    }

                    // Si la cuenta está inactiva o la última solicitud fue rechazada
                    if (sessionData.activo === '0' || sessionData.estadoSolicitud === 'rechazado') {
                        Swal.fire({
                            title: 'Cuenta Desactivada',
                            text: 'Tu cuenta está desactivada. ¿Deseas enviar una solicitud de reactivación?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, enviar solicitud',
                            cancelButtonText: 'No, cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                enviarSolicitudReactivacion();
                            }
                        });
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Error al procesar la solicitud',
                        icon: 'error'
                    });
                }
            });
        });
    }

    // Función para enviar solicitud de reactivación
    async function enviarSolicitudReactivacion() {
        try {
            const response = await fetch('backend/enviarSolicitudReactivacion.php', {
                method: 'POST'
            });
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: 'Solicitud Enviada',
                    text: 'Tu solicitud de reactivación ha sido enviada. Te contactaremos pronto.',
                    icon: 'success'
                });
            } else if (data.error === 'pending') {
                Swal.fire({
                    title: 'Solicitud en Proceso',
                    text: data.message,
                    icon: 'info'
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.error || 'Error al enviar la solicitud',
                    icon: 'error'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error al enviar la solicitud de reactivación',
                icon: 'error'
            });
        }
    }

    initializeLikeButtons();
});
// ----------------------------------------------------------------------------------------------------------
