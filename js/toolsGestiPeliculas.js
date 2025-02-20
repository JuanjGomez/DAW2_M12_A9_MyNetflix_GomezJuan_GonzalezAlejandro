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
        if(result.isConfirmed) {
            // Si el usuario confirma, enviar el formulario
            document.querySelector('form#deleteForm').submit()
        }
    })
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

// Filtros con Fetch ---------------------------------------------------------------------------------------------------
let tablaPeliculas = document.querySelector("#tablaPeliculas tbody")
let ordenarSelect = document.getElementById("ordenar")
let buscadorInput = document.getElementById("buscador")
let resetFiltrosBtn = document.getElementById("resetFiltros")
let categoriasSelect = document.getElementById("categorias")



function cargarPeliculas(filtro = "", orden = "titulo", categoria = "") {
    fetch(`../backend/fecthPeliculas.php?filtro=${filtro}&orden=${orden}&categoria=${categoria}`)
        .then(response => response.json())
        .then(peliculas => {
            tablaPeliculas.innerHTML = ""
            peliculas.forEach(pelicula => {
                const categoriasHTML = pelicula.generos
                const fila = `
                    <tr>
                        <td>${pelicula.titulo_peli}</td>
                        <td><img src="../${pelicula.poster_peli}" width="100" alt="Poster"></td>
                        <td>${pelicula.fecha_estreno_peli}</td>
                        <td>${pelicula.director_peli}</td>
                        <td>${categoriasHTML}</td>
                        <td>
                            <form method="POST" action="formPelicula.php">
                                <input type="hidden" name="idPeli" value="${pelicula.id_peli}">
                                <button type="submit" class="btn btn-warning">Editar</button>
                            </form>
                            <button class="btn btn-danger" onclick="confirmarDelete(${pelicula.id_peli})">Eliminar</button>
                        </td>
                    </tr>`
                tablaPeliculas.innerHTML += fila
            })
        })
        .catch(error => console.error("Error al cargar películas:", error))
}
// Llamar a la funcion inmediatamente para cargar las peliculas
cargarPeliculas()

// Eventos para los filtros
ordenarSelect.onchange = () => cargarPeliculas(buscadorInput.value, ordenarSelect.value, categoriasSelect.value)
buscadorInput.oninput = () => cargarPeliculas(buscadorInput.value, ordenarSelect.value, categoriasSelect.value)
categoriasSelect.onchange = () => cargarPeliculas(buscadorInput.value, ordenarSelect.value, categoriasSelect.value)

// Evento para el boton "Restablecer filtros"
resetFiltrosBtn.onclick = () => {
    buscadorInput.value = ""
    ordenarSelect.value = ""
    categoriasSelect.value = ""
    cargarPeliculas()
}

// ---------------------------------------------------------------------------------------------------------------------