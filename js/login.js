document.querySelector("#formLogin").addEventListener("submit", function(event) {
    event.preventDefault();

    let formulario = document.querySelector("#formLogin");
    let dados = new URLSearchParams(new FormData(formulario));

    fetch("../php/operacoes_user.php", {
        method: "POST",
        body: dados
    })
    .then(resposta => resposta.json())
    .then(resultado => {
        document.querySelector("#mensagem").textContent = resultado.mensagem;

        if (resultado.autenticado == true) {
            window.location.href = "matriculas.html";
        }
    })
    .catch(erro => {
        document.querySelector("#mensagem").textContent = "Erro ao conectar com o servidor.";
    });
});
