verificarSessao().then(function(autenticado) {
    if (autenticado == false) {
        return;
    }

    fetch("../php/operacoes_user.php", {
        method: "POST",
        body: new URLSearchParams({
            operacao: "verificar_sessao"
        })
    })
    .then(resposta => resposta.json())
    .then(sessao => {
        let dados = new URLSearchParams({
            operacao: "consultar_id",
            id: sessao.id
        });

        fetch("../php/operacoes_user.php", {
            method: "POST",
            body: dados
        })
        .then(resposta => resposta.json())
        .then(usuario => {
            document.querySelector("#nome").value = usuario.nome;
            document.querySelector("#email").value = usuario.email;
            document.querySelector("#cpf").value = usuario.cpf;
            document.querySelector("#telefone").value = usuario.telefone;
        });
    });

    document.querySelector("#formPerfil").addEventListener("submit", function(event) {
        event.preventDefault();

        let dados = new URLSearchParams(new FormData(document.querySelector("#formPerfil")));
        dados.append("operacao", "atualizar");

        fetch("../php/operacoes_user.php", {
            method: "POST",
            body: dados
        })
        .then(resposta => resposta.json())
        .then(resultado => {
            document.querySelector("#mensagem").textContent = resultado.mensagem;
        });
    });
});


document.querySelector("#excluirConta").addEventListener("click", function() {
    let confirmar = window.confirm("Tem certeza de que deseja excluir sua conta?");

    if (confirmar == true) {
        fetch("../php/operacoes_user.php", {
            method: "POST",
            body: new URLSearchParams({
                operacao: "excluir"
            })
        })
        .then(resposta => resposta.json())
        .then(resultado => {
            document.querySelector("#mensagem").textContent = resultado.mensagem;
            if (resultado.mensagem == "Conta excluída com sucesso.") {
                window.location.href = "main.html";
            }
        });
    }
});
