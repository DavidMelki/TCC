<?php
session_start();

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

try {
    $usuario_id = $_SESSION['usuario_id'] ?? ($_SESSION['usuario']['id'] ?? null);
    $venda_id   = (int)($_POST['venda_id'] ?? 0);

    if (!$usuario_id || $venda_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado ou venda inválida.']);
        exit;
    }

    // Confere se a venda existe
    $stmtVenda = $pdo->prepare("SELECT id FROM vendas WHERE id = :id");
    $stmtVenda->execute([':id' => $venda_id]);
    if (!$stmtVenda->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'Venda não encontrada.']);
        exit;
    }

    // Já favoritou? Então remove. Senão, adiciona.
    $stmtCheck = $pdo->prepare("SELECT id FROM favoritos_vendas WHERE venda_id = :v AND usuario_id = :u");
    $stmtCheck->execute([':v' => $venda_id, ':u' => $usuario_id]);

    if ($stmtCheck->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM favoritos_vendas WHERE venda_id = :v AND usuario_id = :u");
        $stmt->execute([':v' => $venda_id, ':u' => $usuario_id]);
        $favoritado = false;
    } else {
        $stmt = $pdo->prepare("INSERT INTO favoritos_vendas (venda_id, usuario_id) VALUES (:v, :u)");
        $stmt->execute([':v' => $venda_id, ':u' => $usuario_id]);
        $favoritado = true;
    }

    // Total real de favoritos desta venda
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM favoritos_vendas WHERE venda_id = :v");
    $stmtCount->execute([':v' => $venda_id]);

    echo json_encode([
        'status'     => 'success',
        'favoritado' => $favoritado,
        'likes'      => (int)$stmtCount->fetchColumn()
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>