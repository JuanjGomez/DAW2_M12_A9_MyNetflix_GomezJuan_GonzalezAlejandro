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

// Fecth de filtros -----------------------------------------------------------------------------------------

// ----------------------------------------------------------------------------------------------------------