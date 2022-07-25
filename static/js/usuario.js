/*LISTAR DADOS DO USUARIO LOGADO*/
function listarDados(idLogin) {
    $.post(url, {
        "type": "dados_usuarios",
        "idLogin": $.cookie("idLogin")
    }).done(function (retorno) {
        let json = $.parseJSON(retorno);
        let conteudo = "";
        conteudo += `
            <div>
                <h4 id="nome" style="text-align: center">Usuario: ${json.LOGIN}</h4>
            </div>
        `;
        $("#usuario").html(conteudo);
    });
}
/*LISTAR DADOS DOS USUARIOS DA TABELA DO BANCO DE DADOS*/
function listarUsuarios(login) {
    let jsonDados = listarDados("");
    $.post(url, {
        "type": "listar_usuarios",
        "login": login
    }).done(function (retorno) {
        let json = $.parseJSON(retorno);
        let conteudo = "";

        for (let i = 0; i < json.length; i++) {
            conteudo += `            
            <tr>              
                <th scope="col">${json[i].NOME_COMPLETO}</th>
                <th scope="col">${json[i].LOGIN}</th>
                <th scope="col">${json[i].ATIVO}</th>                
                <th scope="col">
                    <a class="w3-button w3-theme w3-margin-top" title="Editar" href="javascript:consultarUsuario(${json[i].USUARIO_ID})">
                        <i class="fas fa-edit"></i>                        
                    </a>
                    <a class="w3-button w3-theme w3-margin-top" title="Excluir" href="javascript:excluirUsuario(${json[i].USUARIO_ID})">
                        <i class="fas fa-user-times"></i>
                    </a>
                </th>
            </tr>
            `;

        }
        $("#listagem_clientes").html(conteudo);
    });
}
/*LISTA O USUARIO DO SELECT OPTION*/
function listarUsuario() {
    $.post(url, {
        "type": "listar_usuarios",
        "login": ""
    }).done(function (retorno) {
        let json = $.parseJSON(retorno);
        let selectCliente = $("#selectcliente");
        selectCliente.empty();
        selectCliente.append("<option value='0'>Escolha um cliente</option>");
        for (let i = 0; i < json.length; i++) {
            selectCliente.append(`<option value="${json[i].USUARIO_ID}">${json[i].LOGIN}</option>`);
        }
    });
}
/*FAZ A CONSULTA CONFORME CLICA NA TA LINHA DA TABELA PESQUISAR_USUARIOS.HTML*/
function consultarUsuario(idLogin) {
    $.post(url, {
        "type": "consultar_usuarios",
        "idLogin": idLogin
    })
        .done(function (retorno) {
            let json = $.parseJSON(retorno);
            if (json.status == 1) {
                $("#nome_completo").val(json.NOME_COMPLETO);
                $("#login").val(json.LOGIN);
                $("#senha").val(json.SENHA);
                $("#ativo").val(json.ATIVO);
                $("input[name=USUARIO_ID]").val(idLogin);
            } else {
                alert(json.error);
            }
        });

}
/*EXCLUI DA DO BANCO DE DADOS*/
function excluirUsuario(idLogin) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })
    swalWithBootstrapButtons.fire({
        title: 'Deseja realmente excluir esse cliente?',
        text: "Ao apagar você não poderá reverter essa situação",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, deletar!',
        cancelButtonText: 'Não, cancelar!',
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            $.post(url, {"type": "excluir_usuarios", "idLogin": idLogin}).done(function (retorno) {
                let json = $.parseJSON(retorno);
                if (json.status == 1) {
                    setTimeout(function () {
                        window.location.reload(true);
                    }, 1800);
                } else {
                    alert(json.error);
                }
            });
            swalWithBootstrapButtons.fire(
                'Delatado!',
                'Você optou em apagar.',
                'success',
            )
        } else if (
            /* Read more about handling dismissals below */
            result.dismiss === Swal.DismissReason.cancel
        ) {
            swalWithBootstrapButtons.fire(
                'Cancelado',
                'Você não excluiu os dados :)',
                'error'
            )
        }
    })
}
/*INICIAR ASFUNÇÕES E JAVASCRIPT*/
$(document).ready(function () {
    listarDados("");
    listarUsuarios("");
    listarUsuario("");
    $('#selectcliente').select2();

    $("#selectcliente").change(function () {
        let idLogin = $("#selectcliente").val();
        if (idLogin != 0) {
            consultarUsuario(idLogin);
        }
    });

    $("button[type=reset]").click(function () {
        if ($("input[name=type]").val() != "cadastro_usuario") {
            window.location = "cadastro_usuarios.html";
        }
        if($("input[name=type]").val() == "editar_usuarios"){
            window.location = "pesquisa_usuarios.html";
        }
    });

    /*SUBMIT PARA CADASTRAR OU EDITAR O FORM HTML*/
    $("form").submit(function (e) {
        e.preventDefault();
        $('button[type=submit]').prop('disabled', true);
        $("#loading").show();
        let formData = new FormData(this);
        $.ajax({
            url: url,
            method: 'post',
            data: formData,
            processData: false,
            enctype: 'multipart/form-data',
            contentType: false,
            success: function (retorno) {
                let json = $.parseJSON(retorno);
                if (json.status == 1) {
                    var timeout = window.setTimeout(window.location.reload, 2200);
                    Swal.fire({
                        icon: 'success',
                        title: '"Sucesso!", "Operação realizada."',
                        showConfirmButton: false,
                        timer: 1800
                    }).then(function () {
                        window.clearTimeout(timeout)
                    });
                } else {
                    alert(json.error);
                }
                $('button[type=submit]').prop('disabled', false);
            },
            timeout: 8000,
            error: function () {
                alert("Erro ao fazer requisição");
            }
        });
    });
});