function showForm(formId) {
    document.querySelectorAll(".form_box").forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}

let eyeicon = document.getElementById("eyeicon");
    let password = document.getElementById("password");

    eyeicon.onclick = function(){
        if(password.type =="password"){
            password.type = "text";
            eyeicon.src = "assets/images/eyeopen.png"
        }else{
            password.type = "password";
            eyeicon.src = "assets/images/eyeclose.png"
        }
}