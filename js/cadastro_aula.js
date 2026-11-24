verificarSessao().then(function(autenticado) {
    if (autenticado == false) {
        return;
    }

    document.querySelector("#formAula").addEventListener("submit", function(event) {
        event.preventDefault();

        let formulario = document.querySelector("#formAula");
        let dados = new URLSearchParams(new FormData(formulario));

        fetch("../php/operacoes_aulas.php", {
            method: "POST",
            body: dados
        })
        .then(resposta => resposta.json())
        .then(resultado => {
            document.querySelector("#mensagem").textContent = resultado.mensagem;
            if (resultado.mensagem == "Aula cadastrada com sucesso.") {
                formulario.reset();
            }
        });
    });
});
