<?php
session_start();
date_default_timezone_set('America_Sao_Paulo');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    $caminhoConexao = __DIR__ . '/../conexao.php';
    if (!file_exists($caminhoConexao)) {
        throw new Exception("Ficheiro de conexão não encontrado.");
    }
    include_once $caminhoConexao;

    $usuarioLogadoId = $_SESSION['usuario']['id'] ?? null;
    $mensagemId = $_POST['mensagem_id'] ?? null;

    if (!$usuarioLogadoId) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão expirada.']);
        exit;
    }

    if (!$mensagemId) {
        echo json_encode(['status' => 'error', 'message' => 'ID da mensagem não informado.']);
        exit;
    }

    // Apenas o remetente pode apagar a sua própria mensagem
    $stmt = $pdo->prepare("DELETE FROM mensagens WHERE id = :id AND remetente_id = :meuId");
    $stmt->execute([
        ':id' => $mensagemId,
        ':meuId' => $usuarioLogadoId
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Mensagem apagada com sucesso.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Não foi possível apagar esta mensagem.']);
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>