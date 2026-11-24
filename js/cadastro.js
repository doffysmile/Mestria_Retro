document.querySelector("#formCadastro").addEventListener("submit", function(event) {
    event.preventDefault();

    let formulario = document.querySelector("#formCadastro");
    let dados = new URLSearchParams(new FormData(formulario));

    fetch("../php/operacoes_user.php", {
        method: "POST",
        body: dados
    })
    .then(resposta => resposta.json())
    .then(resultado => {
        document.querySelector("#mensagem").textContent = resultado.mensagem;

        if (resultado.mensagem == "Usuário cadastrado com sucesso.") {
            formulario.reset();
        }
    })
    .catch(erro => {
        document.querySelector("#mensagem").textContent = "Erro ao conectar com o servidor.";
    });
});
