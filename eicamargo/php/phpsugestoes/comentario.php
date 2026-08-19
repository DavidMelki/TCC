<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'eicamargo'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sugestao_id = $_POST['sugestao_id'] ?? null;
    $comentario = $_POST['comentario'] ?? null;
    $nome = $_POST['nome'] ?? 'Você';

    if (!$sugestao_id || !$comentario) {
        echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO comentarios (sugestao_id, nome, comentario) VALUES (:sugestao_id, :nome, :comentario)");
    $stmt->execute([
        ':sugestao_id' => $sugestao_id,
        ':nome' => $nome,
        ':comentario' => $comentario
    ]);

    // PEGA O ID DO COMENTÁRIO CRIADO E ENVIA JUNTO NO JSON
    $novoId = $pdo->lastInsertId();

    echo json_encode([
        'status' => 'success',
        'id' => $novoId // <--- ISSO RESOLVE O ERRO DA LIXEIRA
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar comentário: ' . $e->getMessage()]);
}
?>