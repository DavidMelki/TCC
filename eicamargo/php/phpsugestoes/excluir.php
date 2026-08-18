<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'eicamargo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sugestaoId = $_POST['sugestao_id'] ?? 0;

    if (empty($sugestaoId)) {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM sugestoes WHERE id = :id");
    $stmt->execute([':id' => $sugestaoId]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Sugestão excluída com sucesso!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao excluir: ' . $e->getMessage()
    ]);
}
?>