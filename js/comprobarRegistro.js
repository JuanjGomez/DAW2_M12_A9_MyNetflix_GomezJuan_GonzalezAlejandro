// Verificar username
document.getElementById('username').onkeyup = () => {
    let username = this.value.trim()
    let errorUsername = ""

    if(username.length == 0 || username == null || /^\s+$/.test(username)) {
        errorUsername = "El campo no puede estar vacio."
    }else if(username.length < 4){
        errorUsername = "El username debe tener al menos 4 caracteres."
    }else if(!letrasYnumeros(username)){
        errorUsername = "El username solo puede contener letras y numeros."
    }
    function letrasYnumeros(username) {
        return /^[a-zA-Z0-9]+$/.test(username)
    }

    document.getElementById('errorUsername').innerHTML = errorUsername
    verificarForm()
}
// Verificar email
document.getElementById('email').onkeyup = () => {
    let email = this.value.trim()
    let errorEmail = ""

    if(email.length == 0 || email == null || /^\s+$/.test(email)){
        errorEmail = "El campo no puede estar vacio."
    }else if(!emailValido(email)){
        errorEmail = "El email no es valido."
    }
    function emailValido(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
    }

    document.getElementById('errorEmail').innerHTML = errorEmail
    verificarForm()
}
// Veridicar Contrasena
document.getElementById("pwd").onkeyup = () => {
    let pwd = this.value.trim()
    let errorPwd = ""

    if(pwd.length == 0 || pwd == null || /^\s+$/.test(pwd)){
        errorPwd = "El campo no puede estar vacio."
    }else if(pwd.length > 6){
        errorPwd = "La campo debe tener al menos 6 caracteres."
    }else if(!patron(pwd)){
        errorPwd = "La campo debe tener al menos mayúscula, una minúscula y un número."
    }
    function patron(pwd){
        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}/.test(pwd)
    }

    document.getElementById('errorPwd').innerHTML = errorPwd
    verificarForm()
} 
// Verificar si las contrasenas son iguales
document.getElementById('rPwd').onkeyup = () => {
    let pwd = document.getElementById('pwd').value
    let rPwd = this.value.trim()
    let errorRPwd = ""

    if(rPwd == null || rPwd.length == 0 || /^\s+$/.test(rPwd)){
        errorRPwd = "El campo no puede estar vacio."
    }else if(pwd !== rPwd){
        errorRPwd = "Las contraseñas no coinciden."
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
        document.getElementById('username'),
        document.getElementById('email'),
        document.getElementById('pwd'),
        document.getElementById('rPwd')
    ]
    const hayErrores = errores.some(error => error != "")
    const camposVacios = campos.some(campo => campo == "")
    document.getElementById('btn-registro').disabled = hayErrores || camposVacios
}