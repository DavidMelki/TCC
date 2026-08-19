<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$dbname = 'eicamargo'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $comentario_id = $_POST['comentario_id'] ?? null;

    if (!$comentario_id) {
        echo json_encode(['status' => 'error', 'message' => 'ID do comentário não informado.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = :id");
    $stmt->execute([':id' => $comentario_id]);

    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir: ' . $e->getMessage()]);
}
?>