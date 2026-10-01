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

    // Força o fuso horário no banco MySQL para -03:00 (Brasília)
    $pdo->exec("SET time_zone = '-03:00'");

    $usuarioLogadoId = $_SESSION['usuario']['id'] ?? null;
    $destinatarioId = $_POST['destinatario_id'] ?? null;
    $textoMensagem = trim($_POST['mensagem'] ?? '');

    if (!$usuarioLogadoId) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão expirada. Faça login novamente.']);
        exit;
    }

    if (!$destinatarioId || empty($textoMensagem)) {
        echo json_encode(['status' => 'error', 'message' => 'Destinatário ou mensagem ausentes.']);
        exit;
    }

    $dataAtual = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        INSERT INTO mensagens (remetente_id, destinatario_id, mensagem, data_envio) 
        VALUES (:remetente_id, :destinatario_id, :mensagem, :data_envio)
    ");
    $stmt->execute([
        ':remetente_id' => $usuarioLogadoId,
        ':destinatario_id' => $destinatarioId,
        ':mensagem' => $textoMensagem,
        ':data_envio' => $dataAtual
    ]);

    echo json_encode([
        'status' => 'success',
        'hora' => date('H:i')
    ]);

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro: ' . $e->getMessage()]);
}
?>