function send_login_data(){
    email = document.formLogin.email.value;
    contrasena = document.formLogin.contrasena.value;
    regexEmail = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    valid = regexEmail.test(email);

    if (valid && contrasena.length >= 4) {
        url = "http://localhost/motor/login.php?email="+email+"&contrasena="+contrasena;
        location.href=url;
    } 
    
    else {
        document.getElementById("email").style.boxShadow = "5px 5px 5px lightblue";
        document.getElementById("contrasena").style.boxShadow= "5px 5px 5px lightblue";
        alert('Inicio de sesión invalido.');
    }
}

function gray_button(id){
    document.getElementById(id).style.boxShadow = "none";
}
