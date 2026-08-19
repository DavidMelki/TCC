<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$dbname = 'eicamargo'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sugestao_id = $_POST['sugestao_id'] ?? null;
    $acao = $_POST['acao'] ?? null; // 'curtir' ou 'descurtir'

    if (!$sugestao_id || !$acao) {
        echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
        exit;
    }

    if ($acao === 'curtir') {
        // Incrementa 1 no like
        $stmt = $pdo->prepare("UPDATE sugestoes SET likes = likes + 1 WHERE id = :id");
    } else {
        // Decrementa 1 no like (garantindo que não fique menor que 0)
        $stmt = $pdo->prepare("UPDATE sugestoes SET likes = GREATEST(0, likes - 1) WHERE id = :id");
    }
    
    $stmt->execute([':id' => $sugestao_id]);

    // Retorna a quantidade atualizada de likes
    $stmtSelect = $pdo->prepare("SELECT likes FROM sugestoes WHERE id = :id");
    $stmtSelect->execute([':id' => $sugestao_id]);
    $resultado = $stmtSelect->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'likes' => $resultado['likes'] ?? 0
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>