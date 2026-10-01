<?php
session_start();
date_default_timezone_set('America_Sao_Paulo');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    $caminhoConexao = __DIR__ . '/../conexao.php';
    if (!file_exists($caminhoConexao)) {
        throw new Exception("Arquivo de conexão não encontrado.");
    }
    include_once $caminhoConexao;

    $pdo->exec("SET time_zone = '-03:00'");

    $usuarioLogadoId = $_SESSION['usuario']['id'] ?? null;
    $outroUsuarioId = $_GET['usuario_id'] ?? null;

    if (!$usuarioLogadoId || !$outroUsuarioId) {
        echo json_encode(['status' => 'error', 'message' => 'Parâmetros inválidos.']);
        exit;
    }

    // Marca as mensagens recebidas como lidas
    $stmtUpdate = $pdo->prepare("
        UPDATE mensagens 
        SET lida = 1 
        WHERE remetente_id = :outroId AND destinatario_id = :meuId AND lida = 0
    ");
    $stmtUpdate->execute([
        ':outroId' => $outroUsuarioId,
        ':meuId' => $usuarioLogadoId
    ]);

    // Busca as mensagens
    $stmt = $pdo->prepare("
        SELECT id, remetente_id, destinatario_id, mensagem, data_envio
        FROM mensagens
        WHERE (remetente_id = :meuId AND destinatario_id = :outroId)
           OR (remetente_id = :outroId AND destinatario_id = :meuId)
        ORDER BY data_envio ASC
    ");
    $stmt->execute([
        ':meuId' => $usuarioLogadoId,
        ':outroId' => $outroUsuarioId
    ]);

    $mensagensBrutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mensagens = array_map(function($m) {
        $dt = new DateTime($m['data_envio']);
        $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
        return [
            'id' => $m['id'],
            'remetente_id' => $m['remetente_id'],
            'destinatario_id' => $m['destinatario_id'],
            'mensagem' => $m['mensagem'],
            'hora' => $dt->format('H:i')
        ];
    }, $mensagensBrutas);

    echo json_encode([
        'status' => 'success',
        'meu_id' => (int)$usuarioLogadoId,
        'mensagens' => $mensagens
    ]);

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>