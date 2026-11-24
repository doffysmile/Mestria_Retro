verificarSessao().then(function(autenticado) {
    if (autenticado == false) {
        return;
    }

    fetch("../php/operacoes_aulas.php", {
        method: "POST",
        body: new URLSearchParams({
            operacao: "consulta"
        })
    })
    .then(resposta => resposta.json())
    .then(aulas => {
        let resultado = "";

        for (let i = 0; i < aulas.length; i++) {
            resultado += "<div>";
            resultado += "<h3>" + aulas[i].nome + "</h3>";
            resultado += "<p>Horário: " + aulas[i].horario + "</p>";
            resultado += "<p>Assunto: " + aulas[i].assunto + "</p>";
            resultado += "<p>Valor: R$ " + aulas[i].valor + "</p>";
            resultado += "<p>Data: " + aulas[i].data_aula + "</p>";
            resultado += "<p>Vagas: " + aulas[i].capacidade + "</p>";
            resultado += "<button type='button' data-matricular='" + aulas[i].id + "'>Matricular-se</button> ";
            resultado += "<a href='editar_aula.html?id=" + aulas[i].id + "'>Editar</a> ";
            resultado += "<button type='button' data-excluir='" + aulas[i].id + "'>Excluir</button>";
            resultado += "</div><hr>";
        }

        if (aulas.length == 0) {
            resultado = "Nenhuma aula cadastrada.";
        }

        document.querySelector("#resultado").innerHTML = resultado;

        let botoesMatricula = document.querySelectorAll("[data-matricular]");
        for (let i = 0; i < botoesMatricula.length; i++) {
            botoesMatricula[i].addEventListener("click", function() {
                let dados = new URLSearchParams({
                    operacao: "cadastrar",
                    id_aula: this.dataset.matricular
                });

                fetch("../php/operacoes_matriculas.php", {
                    method: "POST",
                    body: dados
                })
                .then(resposta => resposta.json())
                .then(resposta => {
                    document.querySelector("#mensagem").textContent = resposta.mensagem;
                });
            });
        }

        let botoesExcluir = document.querySelectorAll("[data-excluir]");
        for (let i = 0; i < botoesExcluir.length; i++) {
            botoesExcluir[i].addEventListener("click", function() {
                let dados = new URLSearchParams({
                    operacao: "excluir",
                    id: this.dataset.excluir
                });

                fetch("../php/operacoes_aulas.php", {
                    method: "POST",
                    body: dados
                })
                .then(resposta => resposta.json())
                .then(resposta => {
                    document.querySelector("#mensagem").textContent = resposta.mensagem;

                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                });
            });
        }
    })
    .catch(erro => {
        document.querySelector("#resultado").textContent = "Não foi possível carregar as aulas.";
    });
});
