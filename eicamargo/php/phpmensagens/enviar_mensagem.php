<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    $caminhoConexao = __DIR__ . '/../conexao.php';
    if (!file_exists($caminhoConexao)) {
        throw new Exception("Ficheiro de conexão não encontrado.");
    }
    include_once $caminhoConexao;

    $pdo->exec("SET time_zone = '-03:00'");

    $usuarioLogadoId = $_SESSION['usuario']['id'] ?? null;
    $destinatarioId = $_POST['destinatario_id'] ?? null;
    $textoMensagem = trim($_POST['mensagem'] ?? '');

    if (!$usuarioLogadoId) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão expirada. Faça login novamente.']);
        exit;
    }

    if (!$destinatarioId) {
        echo json_encode(['status' => 'error', 'message' => 'Destinatário ausente.']);
        exit;
    }

    $nomeArquivoSalvo = null;

    // Processa o upload do anexo/imagem se houver
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $extensao = strtolower(pathinfo($_FILES['anexo']['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'];

        if (in_array($extensao, $extensoesPermitidas)) {
            $pastaUpload = __DIR__ . '/../../uploads/anexos/';
            if (!file_exists($pastaUpload)) {
                mkdir($pastaUpload, 0777, true);
            }

            $nomeArquivoSalvo = uniqid('anexo_') . '.' . $extensao;
            $caminhoDestino = $pastaUpload . $nomeArquivoSalvo;

            if (!move_uploaded_file($_FILES['anexo']['tmp_name'], $caminhoDestino)) {
                throw new Exception("Falha ao salvar o arquivo enviado.");
            }
        }
    }

    if (empty($textoMensagem) && empty($nomeArquivoSalvo)) {
        echo json_encode(['status' => 'error', 'message' => 'Digite uma mensagem ou selecione uma imagem.']);
        exit;
    }

    $dataAtual = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        INSERT INTO mensagens (remetente_id, destinatario_id, mensagem, anexo, data_envio) 
        VALUES (:remetente_id, :destinatario_id, :mensagem, :anexo, :data_envio)
    ");
    $stmt->execute([
        ':remetente_id' => $usuarioLogadoId,
        ':destinatario_id' => $destinatarioId,
        ':mensagem' => $textoMensagem,
        ':anexo' => $nomeArquivoSalvo,
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