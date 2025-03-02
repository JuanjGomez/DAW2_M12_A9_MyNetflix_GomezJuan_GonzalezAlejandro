// Filtros con Fetch ---------------------------------------------------------------------------------------------------
let tablaPeliculas = document.querySelector("#tablaPeliculas tbody")
let ordenarSelect = document.getElementById("ordenar")
let buscadorInput = document.getElementById("buscador")
let resetFiltrosBtn = document.getElementById("resetFiltros")
let categoriasSelect = document.getElementById("categorias")



function cargarPeliculas(filtro = "", orden = "titulo", categoria = "") {
    fetch(`../backend/fecthPeliculas.php?filtro=${encodeURIComponent(filtro)}&orden=${encodeURIComponent(orden)}&categoria=${encodeURIComponent(categoria)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(peliculas => {
            if (!Array.isArray(peliculas)) {
                if (peliculas.error) {
                    throw new Error(peliculas.error);
                }
                throw new Error('Formato de respuesta inválido');
            }

            tablaPeliculas.innerHTML = "";
            peliculas.forEach(pelicula => {
                const fila = `
                    <tr>
                        <td>${pelicula.titulo_peli}</td>
                        <td><img src="../${pelicula.poster_peli}" width="100" alt="Poster"></td>
                        <td id="fechaEstreno">${pelicula.fecha_estreno_peli}</td>
                        <td id="sinopsis">${pelicula.descripcion_peli}</td>
                        <td id="director">${pelicula.director_peli}</td>
                        <td>${pelicula.likes_peli || 0}</td>
                        <td>${pelicula.generos || ''}</td>
                        <td>
                            <form method="POST" action="formPelicula.php">
                                <input type="hidden" name="idPeli" value="${pelicula.id_peli}">
                                <button type="submit" class="btn btn-warning">Editar</button>
                            </form><p></p>
                            <button class="btn btn-danger" onclick="confirmarDelete(${pelicula.id_peli})">Eliminar</button>
                        </td>
                    </tr>`;
                tablaPeliculas.innerHTML += fila;
            });
        })
        .catch(error => {
            console.error("Error al cargar películas:", error);
            tablaPeliculas.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        Error al cargar las películas. Por favor, intente de nuevo.
                    </td>
                </tr>`;
        });
}

// Event listeners
ordenarSelect.addEventListener('change', function() {
    cargarPeliculas(buscadorInput.value, this.value, categoriasSelect.value);
});

buscadorInput.addEventListener('input', function() {
    cargarPeliculas(this.value, ordenarSelect.value, categoriasSelect.value);
});

categoriasSelect.addEventListener('change', function() {
    cargarPeliculas(buscadorInput.value, ordenarSelect.value, this.value);
});

resetFiltrosBtn.addEventListener('click', function() {
    buscadorInput.value = "";
    ordenarSelect.value = "";
    categoriasSelect.value = "";
    cargarPeliculas();
});

// Cargar películas inicialmente
cargarPeliculas();

// ---------------------------------------------------------------------------------------------------------------------

// SweetAlerts ---------------------------------------------------------------------------------------------------------
// Alert para confirmar si dar pase a elimnar si o no a la pelicula
function confirmarDelete(idPeli) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción eliminará permanentemente la película",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Si el usuario confirma, enviar el formulario
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '../backend/deletePeli.php';
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'idPeli';
            input.value = idPeli;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Alert para informar que fue un exito la eliminacion de pelicula
if(typeof deleteCorrect !== 'undefined' && deleteCorrect){
    Swal.fire({
        title: 'Película eliminada!',
        text: 'La película ha sido eliminada correctamente.',
        icon:'success',
        confirmButtonText: 'Aceptar'
    })
}

// Alert para informar que fue un exito al crear una pelicula
if(typeof peliculaCreada !== 'undefined' && peliculaCreada){
    Swal.fire({
        title: 'Película creada!',
        text: 'La película ha sido creada correctamente.',
        icon:'success',
        confirmButtonText: 'Aceptar'
    })
}

// Alert para informar que fue un exito al editar una pelicula
if(typeof edicionExitosa !== 'undefined' && edicionExitosa){
    Swal.fire({
        title: 'Película editada!',
        text: 'La película ha sido editada correctamente.',
        icon:'success',
        confirmButtonText: 'Aceptar'
    })
}

// ---------------------------------------------------------------------------------------------------------------------