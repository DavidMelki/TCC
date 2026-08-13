<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo  = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
    $preco   = filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_SPECIAL_CHARS);
    $local   = filter_input(INPUT_POST, 'local', FILTER_SANITIZE_SPECIAL_CHARS);
    $legenda = filter_input(INPUT_POST, 'legenda', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($titulo) || empty($preco) || empty($legenda)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Preencha todos os campos obrigatórios.'
        ]);
        exit;
    }

    $caminhoArquivo = null;

    // Processamento de Upload de Mídia (Foto / Vídeo)
    if (isset($_FILES['midia']) && $_FILES['midia']['error'] === UPLOAD_ERR_OK) {
        $diretorioUpload = 'uploads/';
        
        if (!is_dir($diretorioUpload)) {
            mkdir($diretorioUpload, 0755, true);
        }

        $extensao = strtolower(pathinfo($_FILES['midia']['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov', 'avi'];

        if (in_array($extensao, $extensoesPermitidas)) {
            $novoNome = uniqid('venda_', true) . '.' . $extensao;
            $caminhoFinal = $diretorioUpload . $novoNome;

            if (move_uploaded_file($_FILES['midia']['tmp_name'], $caminhoFinal)) {
                $caminhoArquivo = $caminhoFinal;
            }
        }
    }

    // =========================================================================
    // FUTURO BANCO DE DADOS:
    // Aqui você executará o INSERT com $titulo, $preco, $local, $legenda e $caminhoArquivo
    // =========================================================================

    echo json_encode([
        'status'  => 'success',
        'message' => 'Sua venda foi publicada com sucesso!'
    ]);
    exit;
}

echo json_encode([
    'status'  => 'error',
    'message' => 'Método de requisição inválido.'
]);