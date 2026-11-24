verificarSessao().then(function(autenticado) {
    if (autenticado == false) {
        return;
    }

    fetch("../php/operacoes_matriculas.php", {
        method: "POST",
        body: new URLSearchParams({
            operacao: "consulta"
        })
    })
    .then(resposta => resposta.json())
    .then(matriculas => {
        let resultado = "";

        for (let i = 0; i < matriculas.length; i++) {
            resultado += "<div>";
            resultado += "<h3>" + matriculas[i].nome + "</h3>";
            resultado += "<p>Assunto: " + matriculas[i].assunto + "</p>";
            resultado += "<p>Data: " + matriculas[i].data_aula + "</p>";
            resultado += "<p>Horário: " + matriculas[i].horario + "</p>";
            resultado += "<p>Status: " + matriculas[i].status + "</p>";

            if (matriculas[i].status == "ativa") {
                resultado += "<button type='button' data-cancelar='" + matriculas[i].id + "'>Cancelar matrícula</button>";
            } else {
                resultado += "<button type='button' data-excluir='" + matriculas[i].id + "'>Excluir matrícula</button>";
            }

            resultado += "</div><hr>";
        }

        if (matriculas.length == 0) {
            resultado = "Você ainda não possui matrículas.";
        }

        document.querySelector("#resultado").innerHTML = resultado;

        let botoesExcluir = document.querySelectorAll("[data-excluir]");

        for (let i = 0; i < botoesExcluir.length; i++) {
            botoesExcluir[i].addEventListener("click", function() {

                let confirmar = window.confirm("Deseja excluir definitivamente esta matrícula?");

                if (confirmar == true) {

                    let dados = new URLSearchParams({
                        operacao: "excluir",
                        id_aula: this.dataset.excluir
                    });

                    fetch("../php/operacoes_matriculas.php", {
                        method: "POST",
                        body: dados
                    })
                    .then(resposta => resposta.json())
                    .then(resposta => {
                        document.querySelector("#mensagem").textContent = resposta.mensagem;
                        window.location.reload();
                    });
                }
            });
        }

        let botoes = document.querySelectorAll("[data-cancelar]");

        for (let i = 0; i < botoes.length; i++) {
            botoes[i].addEventListener("click", function() {

                let dados = new URLSearchParams({
                    operacao: "atualizar",
                    id_aula: this.dataset.cancelar,
                    status: "cancelada"
                });

                fetch("../php/operacoes_matriculas.php", {
                    method: "POST",
                    body: dados
                })
                .then(resposta => resposta.json())
                .then(resposta => {
                    document.querySelector("#mensagem").textContent = resposta.mensagem;
                    window.location.reload();
                });
            });
        }
    })
    .catch(erro => {
        document.querySelector("#resultado").textContent = "Não foi possível carregar suas matrículas.";
    });
});
