$(document).ready(function() {
    $.post(url, {
        type: "checkCookie"
    }).done(function(retorno) {
        let json = $.parseJSON(retorno);
        if (json.status == 0) {
            window.location = "index.html";
        }
    });
});

function sair() {
    $.post(url, {
        "type": "logout"
    }).done(function(retorno) {
        let json = $.parseJSON(retorno)
        if (json.status == 1) window.location = "index.html";
    });
}

