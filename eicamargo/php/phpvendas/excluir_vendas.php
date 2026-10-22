
<?php
session_start();

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

try {
    $usuario_id = $_SESSION['usuario_id'] ?? ($_SESSION['usuario']['id'] ?? null);
    $venda_id   = (int)($_POST['venda_id'] ?? 0);

    if (!$usuario_id || $venda_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
        exit;
    }

    // Confere se a venda existe e pertence ao usuário logado
    $stmtCheck = $pdo->prepare("SELECT usuario_id, midia FROM vendas WHERE id = :id");
    $stmtCheck->execute([':id' => $venda_id]);
    $venda = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$venda || (int)$venda['usuario_id'] !== (int)$usuario_id) {
        echo json_encode(['status' => 'error', 'message' => 'Você só pode excluir suas próprias vendas.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM vendas WHERE id = :id AND usuario_id = :uid");
    $stmt->execute([':id' => $venda_id, ':uid' => $usuario_id]);

    // Apaga também a foto/vídeo da pasta uploads (basename evita caminhos maliciosos)
    if (!empty($venda['midia'])) {
        $arquivo = __DIR__ . '/../../uploads/' . basename($venda['midia']);
        if (is_file($arquivo)) {
            @unlink($arquivo);
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Venda excluída com sucesso!']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
}
?>