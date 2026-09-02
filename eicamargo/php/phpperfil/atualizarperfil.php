<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $biografia = filter_input(INPUT_POST, 'biografia', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($nome)) {
            echo json_encode(['status' => 'error', 'message' => 'O nome não pode estar vazio.']);
            exit;
        }

        $diretorioUpload = __DIR__ . '/../../uploads/';

        if (!is_dir($diretorioUpload)) {
            mkdir($diretorioUpload, 0755, true);
        }

        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

        // 1. Processamento da Foto de Perfil
        $nomeFoto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensaoFoto = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($extensaoFoto, $extensoesPermitidas)) {
                $nomeFoto = uniqid('perfil_', true) . '.' . $extensaoFoto;
                move_uploaded_file($_FILES['foto']['tmp_name'], $diretorioUpload . $nomeFoto);
            }
        }

        // 2. Processamento da Foto de Capa / Banner
        $nomeCapa = null;
        if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
            $extensaoCapa = strtolower(pathinfo($_FILES['capa']['name'], PATHINFO_EXTENSION));
            if (in_array($extensaoCapa, $extensoesPermitidas)) {
                $nomeCapa = uniqid('capa_', true) . '.' . $extensaoCapa;
                move_uploaded_file($_FILES['capa']['tmp_name'], $diretorioUpload . $nomeCapa);
            }
        }

        // 3. Atualização no Banco de Dados
        $sql = "UPDATE usuarios SET nome = :nome, biografia = :biografia";
        $params = [
            ':nome' => $nome,
            ':biografia' => $biografia,
            ':id' => $usuario_id
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
        echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
        exit;
    }
}
?>