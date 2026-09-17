<?php
session_start();
header('Content-Type: application/json');

include_once '../conexao.php';

try {
    $sugestaoId = $_POST['sugestao_id'] ?? 0;
    $usuario_id = $_SESSION['usuario']['id'] ?? null;

    if (empty($sugestaoId) || !$usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
        exit;
    }

    // Verifica se a sugestão é do usuário logado
    $stmtCheck = $pdo->prepare("SELECT usuario_id FROM sugestoes WHERE id = :id");
    $stmtCheck->execute([':id' => $sugestaoId]);
    $sugestao = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$sugestao || $sugestao['usuario_id'] != $usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Você só pode excluir suas próprias sugestões.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM sugestoes WHERE id = :id");
    $stmt->execute([':id' => $sugestaoId]);

    echo json_encode(['status' => 'success', 'message' => 'Sugestão excluída com sucesso!']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
}
?>