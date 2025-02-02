// Formulario -------------------------------------------------------------------------------------------------------
// Verificar Username
document.getElementById("username").onblur = function () {
    let username = this.value.trim()
    let errorUsername = ""
    let inputUsername = document.getElementById("username")
    
    if(username.length == 0 || username == null || /^\s+$/.test(username)) { // Comprobar si el campo esta vacio
        errorUsername = "El campo no puede esta vacio."
        inputUsername.style.border = "2px solid red"
        
    } else if (!letrasYnumeros(username)){
        errorUsername = "El username solo puede tener letras y numeros."
        inputUsername.style.border = "2px solid red"
    } else {
        inputUsername.style.border = ""
    }

    function letrasYnumeros(username){
        return /^[a-zA-Z0-9]+$/.test(username)
    }

    document.getElementById("errorUsername").innerHTML = errorUsername
    verificarForm()
}
// Verificar contrasena
document.getElementById("pwd").onblur = function () {
    let pwd = this.value.trim()
    let errorPwd = ""
    let inputPwd = document.getElementById("pwd")

    if(pwd == null || pwd.length == 0 || /^\s+$/.test(pwd)) {
        errorPwd = "El campo no puede estar vacio."
        inputPwd.style.border = "2px solid red"
    }else{
        inputPwd.style.border = ""
    }

    document.getElementById("errorPwd").innerHTML = errorPwd
    verificarForm()
}

// Verificar Formulario
function verificarForm(){
    const errores = [
        document.getElementById("errorUsername").innerHTML,
        document.getElementById("errorPwd").innerHTML
    ]
    const campos = [
        document.getElementById("username").value.trim(),
        document.getElementById("pwd").value.trim()
    ]
    const camposVacios = campos.some(campo => campo == "")
    const hayErrores = errores.some(error => error != "")
    document.getElementById("btn-sesion").disabled = camposVacios || hayErrores
}
// ------------------------------------------------------------------------------------------------------------------

// SweetsAlerts------------------------------------------------------------------------------------------------------
if(typeof errorLogin !== 'undefined' && errorLogin){
    Swal.fire({
        title: 'Error!',
        text: 'Las credenciales son incorrectas.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
    })
}
// ------------------------------------------------------------------------------------------------------------------