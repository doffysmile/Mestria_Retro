<?php
require_once "conexao.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

$operacao = $_POST["operacao"] ?? "";

if ($operacao == "cadastrar") {
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $cpf = trim($_POST["cpf"] ?? "");
    if ($cpf == "") {
    $cpf = null;
    }
    $telefone = trim($_POST["telefone"] ?? "");

    if ($nome == "" || $email == "" || $senha == "") {
        echo json_encode(["mensagem" => "Preencha nome, email e senha."]);
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome, email, senha_hash, cpf, telefone)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssss", $nome, $email, $senha_hash, $cpf, $telefone);

        if ($stmt->execute()) {
            echo json_encode(["mensagem" => "Usuário cadastrado com sucesso."]);
        } else {
            echo json_encode(["mensagem" => "Erro ao cadastrar usuário: " . $stmt->error]);
        }
        $stmt->close();
    }

} else if ($operacao == "login") {
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    $sql = "SELECT id, nome, email, senha_hash FROM usuarios WHERE email = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    if ($usuario != null && password_verify($senha, $usuario["senha_hash"])) {
        session_regenerate_id(true);
        $_SESSION["id_usuario"] = $usuario["id"];
        $_SESSION["nome_usuario"] = $usuario["nome"];
        echo json_encode(["mensagem" => "Login realizado com sucesso.", "autenticado" => true]);
    } else {
        http_response_code(401);
        echo json_encode(["mensagem" => "Email ou senha incorretos."]);
    }
    $stmt->close();

} else if ($operacao == "verificar_sessao") {
    if (isset($_SESSION["id_usuario"])) {
        echo json_encode([
            "autenticado" => true,
            "id" => $_SESSION["id_usuario"],
            "nome" => $_SESSION["nome_usuario"]
        ]);
    } else {
        echo json_encode(["autenticado" => false]);
    }

} else if ($operacao == "logout") {
    session_unset();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), "", time() - 3600, "/");
    }
    session_destroy();
    echo json_encode(["mensagem" => "Você saiu da sua conta."]);

} else if ($operacao == "consulta") {
    if (!isset($_SESSION["id_usuario"])) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Faça login para consultar usuários."]);
    } else {
    $sql = "SELECT id, nome, email, cpf, telefone FROM usuarios";
    $resultado = $connection->query($sql);
    $usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    echo json_encode($usuarios);
    }

} else if ($operacao == "consultar_id") {
    if (!isset($_SESSION["id_usuario"])) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Faça login para consultar usuários."]);
    } else {
    $id = $_SESSION["id_usuario"];
    $sql = "SELECT id, nome, email, cpf, telefone FROM usuarios WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    echo json_encode($resultado->fetch_assoc());
    $stmt->close();
    }

} else if ($operacao == "atualizar") {
    if (!isset($_SESSION["id_usuario"])) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Faça login para atualizar seu perfil."]);
    } else {
        $id = $_SESSION["id_usuario"];
        $nome = trim($_POST["nome"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $cpf = trim($_POST["cpf"] ?? "");
        if ($cpf == "") {
        $cpf = null;
        }
        $telefone = trim($_POST["telefone"] ?? "");

        $sql = "UPDATE usuarios SET nome = ?, email = ?, cpf = ?, telefone = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $email, $cpf, $telefone, $id);

        if ($stmt->execute()) {
            $_SESSION["nome_usuario"] = $nome;
            echo json_encode(["mensagem" => "Perfil atualizado com sucesso."]);
        } else {
            echo json_encode(["mensagem" => "Erro ao atualizar perfil: " . $stmt->error]);
        }
        $stmt->close();
    }

} else if ($operacao == "excluir") {
    if (!isset($_SESSION["id_usuario"])) {
        http_response_code(401);
        echo json_encode(["mensagem" => "Faça login para excluir sua conta."]);
    } else {
        $id = $_SESSION["id_usuario"];
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            session_unset();
            session_destroy();
            echo json_encode(["mensagem" => "Conta excluída com sucesso."]);
        } else {
            echo json_encode(["mensagem" => "Não foi possível excluir a conta: " . $stmt->error]);
        }
        $stmt->close();
    }

} else {
    http_response_code(400);
    echo json_encode(["mensagem" => "Operação inválida."]);
}

$connection->close();
?>
