<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

try {
    $sugestao_id = $_POST['sugestao_id'] ?? null;
    $usuario_id = $_SESSION['usuario']['id'] ?? null;

    if (!$sugestao_id || !$usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado ou post inválido.']);
        exit;
    }

    // 1. Verifica se o usuário já curtiu esta sugestão
    $stmtCheck = $pdo->prepare("SELECT id FROM curtidas WHERE sugestao_id = :sugestao_id AND usuario_id = :usuario_id");
    $stmtCheck->execute([
        ':sugestao_id' => $sugestao_id,
        ':usuario_id' => $usuario_id
    ]);
    $jaCurtiu = $stmtCheck->fetch();

    if ($jaCurtiu) {
        // Se já curtiu, remove a curtida (Descurtir)
        $stmtDelete = $pdo->prepare("DELETE FROM curtidas WHERE sugestao_id = :sugestao_id AND usuario_id = :usuario_id");
        $stmtDelete->execute([
            ':sugestao_id' => $sugestao_id,
            ':usuario_id' => $usuario_id
        ]);
        $liked = false;
    } else {
        // Se não curtiu, insere a curtida (Curtir)
        $stmtInsert = $pdo->prepare("INSERT INTO curtidas (sugestao_id, usuario_id) VALUES (:sugestao_id, :usuario_id)");
        $stmtInsert->execute([
            ':sugestao_id' => $sugestao_id,
            ':usuario_id' => $usuario_id
        ]);
        $liked = true;
    }

    // 2. Recalcula o total de curtidas reais da tabela curtidas
    $stmtCount = $pdo->prepare("SELECT COUNT(*) AS total FROM curtidas WHERE sugestao_id = :sugestao_id");
    $stmtCount->execute([':sugestao_id' => $sugestao_id]);
    $totalLikes = (int)$stmtCount->fetchColumn();

    // 3. Atualiza o valor fixo na tabela sugestoes
    $stmtUpdate = $pdo->prepare("UPDATE sugestoes SET likes = :likes WHERE id = :id");
    $stmtUpdate->execute([
        ':likes' => $totalLikes,
        ':id' => $sugestao_id
    ]);

    echo json_encode([
        'status' => 'success',
        'liked' => $liked,
        'likes' => $totalLikes
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>