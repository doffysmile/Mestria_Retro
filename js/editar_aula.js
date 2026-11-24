verificarSessao().then(function(autenticado) {
    if (autenticado == false) {
        return;
    }

    let parametros = new URLSearchParams(window.location.search);
    let id_aula = parametros.get("id");

    fetch("../php/operacoes_aulas.php", {
        method: "POST",
        body: new URLSearchParams({
            operacao: "consultar_id",
            id: id_aula
        })
    })
    .then(resposta => resposta.json())
    .then(aula => {
        if (aula == null) {
            document.querySelector("#mensagem").textContent = "Aula não encontrada.";
            return;
        }

        document.querySelector("#id").value = aula.id;
        document.querySelector("#nome").value = aula.nome;
        document.querySelector("#horario").value = aula.horario;
        document.querySelector("#assunto").value = aula.assunto;
        document.querySelector("#valor").value = aula.valor;
        document.querySelector("#data_aula").value = aula.data_aula;
        document.querySelector("#capacidade").value = aula.capacidade;
    });

    document.querySelector("#formEditarAula").addEventListener("submit", function(event) {
        event.preventDefault();

        let dados = new URLSearchParams(new FormData(document.querySelector("#formEditarAula")));

        fetch("../php/operacoes_aulas.php", {
            method: "POST",
            body: dados
        })
        .then(resposta => resposta.json())
        .then(resultado => {
            document.querySelector("#mensagem").textContent = resultado.mensagem;
        });
    });
});
