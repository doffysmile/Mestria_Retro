<?php
require_once "conexao.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["id_usuario"])) {
    http_response_code(401);
    echo json_encode(["mensagem" => "Faça login para acessar as aulas."]);
    exit;
}

$operacao = $_POST["operacao"] ?? "";

if ($operacao == "cadastrar") {
    $nome = trim($_POST["nome"] ?? "");
    $horario = $_POST["horario"] ?? "";
    $assunto = trim($_POST["assunto"] ?? "");
    $valor = $_POST["valor"] ?? 0;
    $data_aula = $_POST["data_aula"] ?? "";
    $capacidade = $_POST["capacidade"] ?? 0;

    $sql = "INSERT INTO aulas (nome, horario, assunto, valor, data_aula, capacidade)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssdsi", $nome, $horario, $assunto, $valor, $data_aula, $capacidade);

    if ($stmt->execute()) {
        echo json_encode(["mensagem" => "Aula cadastrada com sucesso."]);
    } else {
        echo json_encode(["mensagem" => "Erro ao cadastrar aula: " . $stmt->error]);
    }
    $stmt->close();

} else if ($operacao == "consulta") {
    $sql = "SELECT id, nome, horario, assunto, valor, data_aula, capacidade FROM aulas";
    $resultado = $connection->query($sql);
    echo json_encode(mysqli_fetch_all($resultado, MYSQLI_ASSOC));

} else if ($operacao == "consultar_id") {
    $id = $_POST["id"] ?? 0;
    $sql = "SELECT id, nome, horario, assunto, valor, data_aula, capacidade FROM aulas WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    echo json_encode($resultado->fetch_assoc());
    $stmt->close();

} else if ($operacao == "atualizar") {
    $id = $_POST["id"] ?? 0;
    $nome = trim($_POST["nome"] ?? "");
    $horario = $_POST["horario"] ?? "";
    $assunto = trim($_POST["assunto"] ?? "");
    $valor = $_POST["valor"] ?? 0;
    $data_aula = $_POST["data_aula"] ?? "";
    $capacidade = $_POST["capacidade"] ?? 0;

    $sql = "UPDATE aulas SET nome = ?, horario = ?, assunto = ?, valor = ?, data_aula = ?, capacidade = ? WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssdsii", $nome, $horario, $assunto, $valor, $data_aula, $capacidade, $id);
    // O bind_param utiliza um tipo para cada valor: s, s, s, d, s, i, i.
    if ($stmt->execute()) {
        echo json_encode(["mensagem" => "Aula atualizada com sucesso."]);
    } else {
        echo json_encode(["mensagem" => "Erro ao atualizar aula: " . $stmt->error]);
    }
    $stmt->close();

} else if ($operacao == "excluir") {
    $id = $_POST["id"] ?? 0;
    $sql = "DELETE FROM aulas WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["mensagem" => "Aula excluída com sucesso."]);
    } else {
        echo json_encode(["mensagem" => "Não foi possível excluir a aula. Verifique se existem matrículas vinculadas."]);
    }
    $stmt->close();

} else {
    http_response_code(400);
    echo json_encode(["mensagem" => "Operação inválida."]);
}

$connection->close();
?>
