function verificarSessao() {
    return fetch("../php/operacoes_user.php", {
        method: "POST",
        body: new URLSearchParams({
            operacao: "verificar_sessao"
        })
    })
    .then(resposta => resposta.json())
    .then(resultado => {
        if (resultado.autenticado == false) {
            window.location.href = "main.html";
            return false;
        }

        let boasVindas = document.querySelector("#boasVindas");
        if (boasVindas != null) {
            boasVindas.textContent = "Olá, " + resultado.nome;
        }

        return true;
    });
}

let botaoSair = document.querySelector("#sair");
if (botaoSair != null) {
    botaoSair.addEventListener("click", function() {
        fetch("../php/operacoes_user.php", {
            method: "POST",
            body: new URLSearchParams({
                operacao: "logout"
            })
        })
        .then(resposta => resposta.json())
        .then(resultado => {
            window.location.href = "main.html";
        });
    });
}
