<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

// Ação de SALVAR VENDA (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['usuario_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
        exit;
    }

    $usuario_id = $_SESSION['usuario_id'];
    $titulo     = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
    $preco      = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_SPECIAL_CHARS);
    $local      = filter_input(INPUT_POST, 'local', FILTER_SANITIZE_SPECIAL_CHARS);
    $legenda    = filter_input(INPUT_POST, 'legenda', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($titulo) || empty($preco) || empty($legenda)) {
        echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos obrigatórios.']);
        exit;
    }

    $caminhoArquivo = null;

    // Processamento de Upload de Mídia (Foto / Vídeo)
    if (isset($_FILES['midia']) && $_FILES['midia']['error'] === UPLOAD_ERR_OK) {
        // Aponta para a pasta uploads na raiz do projeto
        $diretorioUpload = __DIR__ . '/../../uploads/';
        
        if (!is_dir($diretorioUpload)) {
            mkdir($diretorioUpload, 0755, true);
        }

        $extensao = strtolower(pathinfo($_FILES['midia']['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov'];

        if (in_array($extensao, $extensoesPermitidas)) {
            $novoNome = uniqid('venda_', true) . '.' . $extensao;
            $destinoFinal = $diretorioUpload . $novoNome;

            if (move_uploaded_file($_FILES['midia']['tmp_name'], $destinoFinal)) {
                // Salva o caminho relativo que o HTML entende perfeitamente
                $caminhoArquivo = '../uploads/' . $novoNome;
            }
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO vendas (usuario_id, titulo, preco, local, legenda, midia) VALUES (:usuario_id, :titulo, :preco, :local, :legenda, :midia)");
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':titulo'     => $titulo,
            ':preco'      => $preco,
            ':local'      => $local,
            ':legenda'    => $legenda,
            ':midia'      => $caminhoArquivo
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Venda publicada com sucesso!']);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar no banco: ' . $e->getMessage()]);
        exit;
    }
}

// Ação de LISTAR VENDAS (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $sql = "SELECT v.*, u.nome, u.foto_perfil 
                FROM vendas v 
                JOIN usuarios u ON v.usuario_id = u.id 
                ORDER BY v.id DESC";
        $stmt = $pdo->query($sql);
        $vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vendas as &$venda) {
            $foto = trim($venda['foto_perfil'] ?? '');
            if (!empty($foto)) {
                $venda['foto_perfil'] = '/TCC/uploads/' . str_replace([' ', ' png'], ['.', '.png'], $foto);
            } else {
                $venda['foto_perfil'] = '';
            }
        }
        unset($venda);

        echo json_encode(['status' => 'success', 'vendas' => $vendas]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>