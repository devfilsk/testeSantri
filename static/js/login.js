$(document).ready(function() {
    $.post(url, {
        type: "checkCookie"
    }).done(function(retorno) {
        let json = $.parseJSON(retorno);
        if (json.status != 0) {
            window.location = "cadastro_usuarios.html";
        }
    });
    $("form").submit(function(ev) {
        ev.preventDefault();
        let user = $("#user").val();
        let password = $("#password").val();
        $.post(url, {
            "type": "login",
            "user": user,
            "password": password
        }).done(function(retorno) {
            alert(retorno);
            let json = $.parseJSON(retorno);
            if (json.status == 1) window.location = "cadastro_usuarios.html";
                else alert(json.error);
        });
    });
});