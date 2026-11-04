<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

// Salva uma imagem conferindo o tipo REAL do arquivo (não só a extensão)
function salvarImagem(array $arquivo, string $prefixo, string $pasta): string {
    if ($arquivo['error'] === UPLOAD_ERR_INI_SIZE || $arquivo['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new Exception('A imagem é muito grande. Use uma de até 5 MB.');
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Falha no envio da imagem. Tente novamente.');
    }
    if ($arquivo['size'] > 5 * 1024 * 1024) {
        throw new Exception('A imagem deve ter no máximo 5 MB.');
    }

    $permitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif'
    ];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);

    if (!isset($permitidos[$mime])) {
        throw new Exception('Formato inválido. Use JPG, PNG, WEBP ou GIF.');
    }

    $nome = uniqid($prefixo, true) . '.' . $permitidos[$mime];

    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . $nome)) {
        throw new Exception('Não foi possível salvar a imagem no servidor.');
    }

    return $nome;
}

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Envio maior que o post_max_size: o PHP descarta todos os campos
        if (empty($_POST) && empty($_FILES) && ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'A imagem é muito grande. Use uma de até 5 MB.']);
            exit;
        }

        $nome      = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $biografia = filter_input(INPUT_POST, 'biografia', FILTER_SANITIZE_SPECIAL_CHARS);
        $ano       = (int)($_POST['ano'] ?? 0);
        $curso     = trim($_POST['curso'] ?? '');

        if (empty($nome)) {
            echo json_encode(['status' => 'error', 'message' => 'O nome não pode estar vazio.']);
            exit;
        }

        // Ano e curso são obrigatórios; a biografia é opcional
        if (!in_array($ano, [1, 2, 3], true)) {
            echo json_encode(['status' => 'error', 'message' => 'Selecione o seu ano (1º, 2º ou 3º).']);
            exit;
        }

        if ($curso === '') {
            echo json_encode(['status' => 'error', 'message' => 'Informe o seu curso.']);
            exit;
        }

        if (mb_strlen($curso) > 100) {
            echo json_encode(['status' => 'error', 'message' => 'O nome do curso deve ter no máximo 100 caracteres.']);
            exit;
        }

        $diretorioUpload = __DIR__ . '/../../uploads/';

        if (!is_dir($diretorioUpload)) {
            mkdir($diretorioUpload, 0755, true);
        }

        // 1. Foto de perfil
        $nomeFoto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $nomeFoto = salvarImagem($_FILES['foto'], 'perfil_', $diretorioUpload);
        }

        // 2. Foto de capa / banner
        $nomeCapa = null;
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] !== UPLOAD_ERR_NO_FILE) {
            $nomeCapa = salvarImagem($_FILES['capa'], 'capa_', $diretorioUpload);
        }

        // 3. Atualização no Banco de Dados
        $sql = "UPDATE usuarios SET nome = :nome, biografia = :biografia, ano = :ano, curso = :curso";
        $params = [
            ':nome'      => $nome,
            ':biografia' => $biografia ?? '',
            ':ano'       => $ano,
            ':curso'     => $curso,
            ':id'        => $usuario_id
        ];

        if ($nomeFoto) {
            $sql .= ", foto_perfil = :foto";
            $params[':foto'] = $nomeFoto;
        }

        if ($nomeCapa) {
            $sql .= ", foto_capa = :capa";
            $params[':capa'] = $nomeCapa;
        }

        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['status' => 'success', 'message' => 'Perfil atualizado com sucesso!']);
        exit;

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>