<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($descricao)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Por favor, escreva a sua sugestão.'
        ]);
        exit;
    }

    require_once "conexao.php";

    try {
        $sql = "INSERT INTO sugestoes (descricao) VALUES (:descricao)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->execute();

        // Pega o ID gerado para inclusão na tela
        $idInserido = $pdo->lastInsertId();

        echo json_encode([
            'status' => 'success',
            'message' => 'Sugestão cadastrada com sucesso!',
            'dados' => [
                'id' => $idInserido,
                'descricao' => $descricao,
                'tempo' => 'Agora'
            ]
        ]);
        exit;

    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao cadastrar sugestão no banco de dados.'
        ]);
        exit;
    }
}

echo json_encode([
    'status' => 'error',
    'message' => 'Método de requisição inválido.'
]);