<?php
session_start();
header('Content-Type: application/json');

include_once '../conexao.php';

try {
    $sugestao_id = $_POST['sugestao_id'] ?? null;
    $comentario = $_POST['comentario'] ?? null;
    $usuario_id = $_SESSION['usuario']['id'] ?? null;
    $nomeUsuario = $_SESSION['usuario']['nome'] ?? 'Usuário';

    if (!$sugestao_id || !$comentario || !$usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão inválida ou dados incompletos.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO comentarios (sugestao_id, usuario_id, nome, comentario) VALUES (:sugestao_id, :usuario_id, :nome, :comentario)");
    $stmt->execute([
        ':sugestao_id' => $sugestao_id,
        ':usuario_id' => $usuario_id,
        ':nome' => $nomeUsuario,
        ':comentario' => $comentario
    ]);

    $novoId = $pdo->lastInsertId();
    $userTag = '@' . strtolower(str_replace(' ', '', $nomeUsuario));

    echo json_encode([
        'status' => 'success',
        'id' => $novoId,
        'usuario_id' => $usuario_id,
        'nome' => $nomeUsuario,
        'userTag' => $userTag
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>