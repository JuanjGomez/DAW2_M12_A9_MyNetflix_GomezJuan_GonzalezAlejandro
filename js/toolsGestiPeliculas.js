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
if(typeof editarCreada !== 'undefined' && editarCreada){
    Swal.fire({
        title: 'Película editada!',
        text: 'La película ha sido editada correctamente.',
        icon:'success',
        confirmButtonText: 'Aceptar'
    })
}

// ---------------------------------------------------------------------------------------------------------------------