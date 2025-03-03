// Formulario -----------------------------------------------------------------------------------
// Verificar username
document.getElementById('username').oninput = function() {
    let username = this.value.trim()
    let errorUsername = ""

    if(username.length == 0 || username == null || /^\s+$/.test(username)) {
        errorUsername = "El campo no puede estar vacio."
        this.style.border = "2px solid red"
    }else if(username.length < 4){
        errorUsername = "El username debe tener al menos 4 caracteres."
        this.style.border = "2px solid red"
    }else if(!letrasYnumeros(username)){
        errorUsername = "El username solo puede contener letras y numeros."
        this.style.border = "2px solid red"
    } else {
        this.style.border = ""
    }

    function letrasYnumeros(username) {
        return /^[a-zA-Z0-9]+$/.test(username)
    }

    document.getElementById('errorUsername').innerHTML = errorUsername
    verificarForm()
}
// Verificar email
document.getElementById('email').oninput = function() {
    let email = this.value.trim()
    let errorEmail = ""

    if(email.length == 0 || email == null || /^\s+$/.test(email)){
        errorEmail = "El campo no puede estar vacio."
        this.style.border = "2px solid red"
    }else if(!emailValido(email)){
        errorEmail = "El email no es valido."
        this.style.border = "2px solid red"
    } else {
        this.style.border = ""
    }

    function emailValido(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
    }

    document.getElementById('errorEmail').innerHTML = errorEmail
    verificarForm()
}
// Veridicar Contrasena
document.getElementById("pwd").oninput = function() {
    let pwd = this.value.trim()
    let errorPwd = ""

    if(pwd.length == 0 || pwd == null || /^\s+$/.test(pwd)){
        errorPwd = "El campo no puede estar vacio."
        this.style.border = "2px solid red"
    }else if(pwd.length < 6){
        errorPwd = "La campo debe tener al menos 6 caracteres."
        this.style.border = "2px solid red"
    }else if(!patron(pwd)){
        errorPwd = "La campo debe tener al menos mayúscula, una minúscula y un número."
        this.style.border = "2px solid red"
    } else {
        this.style.border = ""
    }

    function patron(pwd){
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}/.test(pwd)
    }

    document.getElementById('errorPwd').innerHTML = errorPwd
    verificarForm()
} 
// Verificar si las contrasenas son iguales
document.getElementById('rPwd').oninput = function() {
    let pwd = document.getElementById('pwd').value
    let rPwd = this.value.trim()
    let errorRPwd = ""

    if(rPwd == null || rPwd.length == 0 || /^\s+$/.test(rPwd)){
        errorRPwd = "El campo no puede estar vacio."
        this.style.border = "2px solid red"
    }else if(pwd !== rPwd){
        errorRPwd = "Las contraseñas no coinciden."
        this.style.border = "2px solid red"
    } else {
        this.style.border = ""
    }

    document.getElementById('errorRpwd').innerHTML = errorRPwd
    verificarForm()
}
// Verificar Formulario
function verificarForm(){
    const errores = [
        document.getElementById('errorUsername').innerHTML,
        document.getElementById('errorEmail').innerHTML,
        document.getElementById('errorPwd').innerHTML,
        document.getElementById('errorRpwd').innerHTML
    ]
    const campos = [
        document.getElementById('username').value.trim(),
        document.getElementById('email').value.trim(),
        document.getElementById('pwd').value.trim(),
        document.getElementById('rPwd').value.trim()
    ]
    const hayErrores = errores.some(error => error != "")
    const camposVacios = campos.some(campo => campo == "")
    document.getElementById('btn-sesion').disabled = hayErrores || camposVacios
}
// ----------------------------------------------------------------------------------------------

// SweetAlerts ----------------------------------------------------------------------------------
// Aviso que la peticion de alta no ha sido revisado por un admin
if(typeof esperaPeticion !== 'undefined' && esperaPeticion) {
    Swal.fire({
        title: 'Usuario a espera!',
        text: 'Por favor, su solicitud de alta sigue pendiente.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
    })
}
// Aviso que ya hay un usuario con mismo username
if(typeof errorCrear !== 'undefined' && errorCrear){
    Swal.fire({
        title: 'Error!',
        text: 'Ya hay un usuario con el mismo username.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
    })
}
// ----------------------------------------------------------------------------------------------