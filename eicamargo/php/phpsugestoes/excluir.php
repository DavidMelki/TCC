<?php
session_start();
header('Content-Type: application/json');

include_once '../conexao.php';
include_once '../moderador.php';

try {
    $sugestaoId = $_POST['sugestao_id'] ?? 0;
    $usuario_id = $_SESSION['usuario']['id'] ?? null;

    if (empty($sugestaoId) || !$usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
        exit;
    }

    $stmtCheck = $pdo->prepare("SELECT usuario_id FROM sugestoes WHERE id = :id");
    $stmtCheck->execute([':id' => $sugestaoId]);
    $sugestao = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$sugestao) {
        echo json_encode(['status' => 'error', 'message' => 'Sugestão não encontrada.']);
        exit;
    }

    // Pode excluir: o próprio autor OU um moderador (diretor/coordenador)
    $ehDono = ((int)$sugestao['usuario_id'] === (int)$usuario_id);

    if (!$ehDono && !ehModerador($pdo, $usuario_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para excluir esta sugestão.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM sugestoes WHERE id = :id");
    $stmt->execute([':id' => $sugestaoId]);

    echo json_encode(['status' => 'success', 'message' => 'Sugestão excluída com sucesso!']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
}
?>