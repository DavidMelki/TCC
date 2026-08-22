<?php
header('Content-Type: application/json; charset=utf-8');

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Você precisa estar logado para publicar.']);
    exit;
}

// Inclui a conexão centralizada (ajuste o caminho se necessário: de php/phpsugestoes/ para php/)
include_once '../conexao.php';

try {
    $descricao = $_POST['descricao'] ?? '';

    if (empty(trim($descricao))) {
        echo json_encode(['status' => 'error', 'message' => 'A descrição não pode estar vazia.']);
        exit;
    }

    $usuario_id = $_SESSION['usuario_id'];

    // Insere vinculando o usuario_id (certifique-se de que a sua tabela sugestoes possui a coluna usuario_id)
    $stmt = $pdo->prepare("INSERT INTO sugestoes (usuario_id, descricao, likes) VALUES (:usuario_id, :descricao, :likes)");
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':descricao'  => $descricao,
        ':likes'      => 0
    ]);

    $idInserido = $pdo->lastInsertId();

    // Opcional: busca os dados do usuário para retornar caso o front precise atualizar na hora
    $stmtUser = $pdo->prepare("SELECT nome, foto_perfil FROM usuarios WHERE id = :id");
    $stmtUser->execute([':id' => $usuario_id]);
    $dadosUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'message' => 'Sugestão cadastrada com sucesso!',
        'dados' => [
            'id' => $idInserido,
            'descricao' => $descricao,
            'nome' => $dadosUser['nome'] ?? 'Usuário',
            'foto_perfil' => $dadosUser['foto_perfil'] ?? 'default.png'
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao salvar no banco: ' . $e->getMessage()
    ]);
}
?>