<?php
require_once "conexao.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["id_usuario"])) {
    http_response_code(401);
    echo json_encode(["mensagem" => "Faça login para acessar suas matrículas."]);
    exit;
}

$id_usuario = $_SESSION["id_usuario"];
$operacao = $_POST["operacao"] ?? "";



if ($operacao == "cadastrar") {
    $id_aula = $_POST["id_aula"];

    // Verifica se já existe uma matrícula
    $sql = "SELECT status FROM matriculas
            WHERE id_usuario = ? AND id_aula = ?";

    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $id_usuario, $id_aula);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $matricula = $resultado->fetch_assoc();
    $stmt->close();

    if ($matricula != null && $matricula["status"] == "ativa") {

        echo json_encode(["mensagem" => "Você já está matriculado nesta aula."]);

    } else {

        // Consulta a capacidade da aula
        $sql = "SELECT capacidade FROM aulas WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id_aula);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $aula = $resultado->fetch_assoc();
        $stmt->close();

        if ($aula == null) {

            echo json_encode(["mensagem" => "Aula não encontrada."]);

        } else {

            // Conta as matrículas ativas
            $sql = "SELECT COUNT(*) AS ocupadas FROM matriculas
                    WHERE id_aula = ? AND status = 'ativa'";

            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id_aula);
            $stmt->execute();

            $resultado = $stmt->get_result();
            $ocupadas = $resultado->fetch_assoc()["ocupadas"];
            $stmt->close();

            if ($ocupadas >= $aula["capacidade"]) {

                echo json_encode(["mensagem" => "Não há vagas disponíveis nesta aula."]);

            } else if ($matricula != null) {

                // Reativa uma matrícula cancelada
                $sql = "UPDATE matriculas
                        SET status = 'ativa',
                            data_matricula = CURRENT_TIMESTAMP
                        WHERE id_usuario = ? AND id_aula = ?";

                $stmt = $connection->prepare($sql);
                $stmt->bind_param("ii", $id_usuario, $id_aula);
                $stmt->execute();

                echo json_encode(["mensagem" => "Matrícula realizada novamente com sucesso."]);
                $stmt->close();

            } else {

                // Cadastra uma nova matrícula
                $sql = "INSERT INTO matriculas (id_usuario, id_aula)
                        VALUES (?, ?)";

                $stmt = $connection->prepare($sql);
                $stmt->bind_param("ii", $id_usuario, $id_aula);
                $stmt->execute();

                echo json_encode(["mensagem" => "Matrícula realizada com sucesso."]);
                $stmt->close();
            }
        }
    }
}else if ($operacao == "consulta") {
 $sql = "SELECT aulas.id, aulas.nome, aulas.horario, aulas.assunto, aulas.valor,
                   aulas.data_aula, matriculas.status, matriculas.data_matricula
            FROM matriculas
            INNER JOIN aulas ON matriculas.id_aula = aulas.id
            WHERE matriculas.id_usuario = ?
            ORDER BY matriculas.data_matricula DESC";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    echo json_encode(mysqli_fetch_all($resultado, MYSQLI_ASSOC));
    $stmt->close();

} else if ($operacao == "atualizar") {
    $id_aula = $_POST["id_aula"] ?? 0;
    $status = $_POST["status"] ?? "cancelada";

    if ($status != "ativa" && $status != "cancelada") {
        echo json_encode(["mensagem" => "Status inválido."]);
    } else {
        $sql = "UPDATE matriculas SET status = ? WHERE id_usuario = ? AND id_aula = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sii", $status, $id_usuario, $id_aula);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(["mensagem" => "Matrícula atualizada com sucesso."]);
        } else {
            echo json_encode(["mensagem" => "Nenhuma matrícula foi alterada."]);
        }
        $stmt->close();
    }

} else if ($operacao == "excluir") {
    $id_aula = $_POST["id_aula"] ?? 0;
    $sql = "DELETE FROM matriculas WHERE id_usuario = ? AND id_aula = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ii", $id_usuario, $id_aula);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(["mensagem" => "Matrícula excluída com sucesso."]);
    } else {
        echo json_encode(["mensagem" => "Matrícula não encontrada."]);
    }
    $stmt->close();

} else {
    http_response_code(400);
    echo json_encode(["mensagem" => "Operação inválida."]);
}

$connection->close();
?>
