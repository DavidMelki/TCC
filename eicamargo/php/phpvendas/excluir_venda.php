<?php
session_start();

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';
include_once '../moderador.php';

try {
    $usuario_id = $_SESSION['usuario_id'] ?? ($_SESSION['usuario']['id'] ?? null);
    $venda_id   = (int)($_POST['venda_id'] ?? 0);

    if (!$usuario_id || $venda_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
        exit;
    }

    $stmtCheck = $pdo->prepare("SELECT usuario_id, midia FROM vendas WHERE id = :id");
    $stmtCheck->execute([':id' => $venda_id]);
    $venda = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$venda) {
        echo json_encode(['status' => 'error', 'message' => 'Venda não encontrada.']);
        exit;
    }

    // Pode excluir: o próprio vendedor OU um moderador (diretor/coordenador)
    $ehDono = ((int)$venda['usuario_id'] === (int)$usuario_id);

    if (!$ehDono && !ehModerador($pdo, $usuario_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para excluir esta venda.']);
        exit;
    }

    $stmtFav = $pdo->prepare("DELETE FROM favoritos_vendas WHERE venda_id = :id");
    $stmtFav->execute([':id' => $venda_id]);

    $stmt = $pdo->prepare("DELETE FROM vendas WHERE id = :id");
    $stmt->execute([':id' => $venda_id]);

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