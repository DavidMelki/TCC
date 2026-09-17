<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

try {
    $comentario_id = $_POST['comentario_id'] ?? null;
    $usuario_id = $_SESSION['usuario']['id'] ?? null;

    if (!$comentario_id || !$usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
        exit;
    }

    // Verifica se o comentário pertence ao usuário logado
    $stmtCheck = $pdo->prepare("SELECT usuario_id FROM comentarios WHERE id = :id");
    $stmtCheck->execute([':id' => $comentario_id]);
    $comentario = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$comentario || $comentario['usuario_id'] != $usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Você só pode excluir seus próprios comentários.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = :id");
    $stmt->execute([':id' => $comentario_id]);

    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
}
?>