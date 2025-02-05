// Formulario ----------------------------------------------------------------------------------------------
// Validacion de titulo
document.getElementById('titulo').onblur = function() {
    let titulo = this.value.trim()
    let errorTitulo = ""

    if(titulo.length == 0 || titulo == null || /^\s+$/.test(titulo)){
        errorTitulo = "El campo no puede estar vacio."
        this.style.border = "2px solid red"
    } else if(titulo.length < 2){
        errorTitulo = "El campo debe tener como minimo 2 caracteres."
        this.style.border = "2px solid red"
    } else {
        this.style.border = ""
    }

    document.getElementById('errorTitulo').innerHTML = errorTitulo
    verificarForm()
}
// Validacion de descripcion
document.getElementById("descripcion").onblur = function() {
    let descripcion = this.value.trim()
    let errorDescripcion = ""

    if(descripcion.length == 0 || descripcion == null || /^\s+$/.test(descripcion)){
        errorDescripcion = "El campo no puede estar vacio."
        this.style.border = "2px solid red"
    } else if(descripcion.length < 10){
        errorDescripcion = "El campo debe tener como minimo 10 caracteres."
        this.style.border = "2px solid red"
    } else {
        this.style.border = ""
    }

    document.getElementById('errorDescripcion').innerHTML = errorDescripcion
    verificarForm()
}
// Validacion de fecha de estreno
document.getElementById("fechaEstreno").onblur = function() {
    let fechaEstreno = this.value.trim()
    let errorFechaEstreno = ""

    const fechaMinima = new Date('1895-12-28')// Fecha minima
    const fechaIngresada = new Date(fechaEstreno)// Fecha que introdujo el usuario

    if(fechaEstreno.length == 0 || fechaEstreno == null || /^\s+$/.test(fechaEstreno)){
        errorFechaEstreno = "El campo no puede estar vacio."
        this.style.border = "2px solid red"
    } else if(fechaIngresada < fechaMinima){
        errorFechaEstreno = "La fecha no puede ser inferior a esta."
        this.style.border = "2px solid red"
    } else {
        this.style.border = ""
    }

    document.getElementById('errorFechaEstreno').innerHTML = errorFechaEstreno
    verificarForm()
}
// Validacion de director
document.getElementById('director').onblur = function() {
    let director = this.value.trim()
    let errorDirector = ""

    if(director.length == 0 || director == null || /^\s+$/.test(director)){
        errorDirector = "El campo no puede estar vacio."
        this.style.border = "2px solid red"
    }else if(director.length < 3){
        errorDirector = "El campo debe tener como mínimo 3 caracteres.";
        this.style.border = "2px solid red";
    } else {
        this.style.border = ""
    }

    document.getElementById('errorDirector').innerHTML = errorDirector
    verificarForm()
}
// Validacion de la imagen que va a ser como poster
document.getElementById("poster").onmouseleave = function() {
    let fileInput = this
    let file = fileInput.files[0]
    let errorPoster = ""

    if(!file){
        errorPoster = "Debe seleccionar una imagen."
        this.style.border = "2px solid red"
    } else if(!file.name.match(/\.(jpg|jpeg|png)$/i)){
        errorPoster = "El archivo debe ser una imagen (JPG, JPEG, PNG)."
        this.style.border = "2px solid red"
    } else {
        this.style.border = ""
    }

    document.getElementById('errorPoster').innerHTML = errorPoster
    verificarForm()
}
// Validacion de las categorias
document.querySelector('fieldset').onmouseleave = function() {
    const checkbox = document.querySelectorAll("input[name='categorias[]']")
    let errorCategorias = ""
    let algunoSeleccionado = false

    // Verificar si algun checkob ha sido seleccionado
    checkbox.forEach(checkbox => {
        if(checkbox.checked) {
            algunoSeleccionado = true
        }
    })

    if(!algunoSeleccionado){
        errorCategorias = "Debe seleccionar al menos una categoría."
        this.style.border = "2px solid red"
    } else {
        this.style.border = "2px solid green"
    }

    document.getElementById('errorCategorias').innerHTML = errorCategorias
    verificarForm()
}
// Validacion de todo formulario
function verificarForm() {
    const errores = [
        document.getElementById('errorTitulo').innerHTML,
        document.getElementById('errorDescripcion').innerHTML,
        document.getElementById('errorFechaEstreno').innerHTML,
        document.getElementById('errorDirector').innerHTML,
        document.getElementById('errorPoster').innerHTML,
        document.getElementById('errorCategorias').innerHTML
    ]
    const campos = [
        document.getElementById('titulo').value.trim(),
        document.getElementById('descripcion').value.trim(),
        document.getElementById('fechaEstreno').value.trim(),
        document.getElementById('director').value.trim(),
        document.getElementById('poster').files.length > 0,
        document.querySelectorAll("input[name='categorias[]']:checked").length > 0
    ]

    const hayErrores = errores.some(error => error != "")
    const camposVacios = campos.some(campo => campo === "" || campo === false)
    document.getElementById('btn-sesion').disabled = hayErrores || camposVacios
}
// ---------------------------------------------------------------------------------------------------------

// SweetAlerts ---------------------------------------------------------------------------------------------
if(typeof errorTituloDuplicado !== 'undefined' && errorTituloDuplicado){
    Swal.fire({
        title: 'Error!',
        text: 'El título ingresado ya se encuentra en uso.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
    })
}
// ---------------------------------------------------------------------------------------------------------