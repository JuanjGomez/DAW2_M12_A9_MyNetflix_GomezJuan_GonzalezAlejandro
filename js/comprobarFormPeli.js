// Formulario ----------------------------------------------------------------------------------------------
// Validacion de titulo
document.getElementById('titulo').onblur = function() {
    let titulo = this.value.trim()
    let errorTitulo = ""
    let inputTitulo = document.getElementById('titulo')

    if(titulo.length == 0 || titulo == null || /^\s+$/.test(titulo)){
        errorTitulo = "El campo no puede estar vacio."
        inputTitulo.style.border = "2px solid red"
    } else if(titulo.length > 2){
        errorTitulo = "El campo debe tener como máximo 3 caracteres."
        inputTitulo.style.border = "2px solid red"
    } else {
        inputTitulo.style.border = ""
    }

    document.getElementById('errorTitulo').innerHTML = errorTitulo
    verificarForm()
}
// Validacion de descripcion
document.getElementById("descripcion").onblur = function() {
    let descripcion = this.value.trim()
    let errorDescripcion = ""
    let inputDescripcion = document.getElementById("descripcion")

    if(descripcion.length == 0 || descripcion == null || /^\s+$/.test(descripcion)){
        errorDescripcion = "El campo no puede estar vacio."
        inputDescripcion.style.border = "2px solid red"
    } else if(descripcion.length > 10){
        errorDescripcion = "El campo debe tener como máximo 10 caracteres."
        inputDescripcion.style.border = "2px solid red"
    } else {
        inputDescripcion.style.border = ""
    }

    document.getElementById('errorDescripcion').innerHTML = errorDescripcion
    verificarForm()
}
// ---------------------------------------------------------------------------------------------------------