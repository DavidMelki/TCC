<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'eicamargo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $descricao = $_POST['descricao'] ?? '';

    if (empty(trim($descricao))) {
        echo json_encode(['status' => 'error', 'message' => 'A descrição não pode estar vazia.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO sugestoes (nome, usuario, descricao, likes) VALUES (:nome, :usuario, :descricao, :likes)");
    $stmt->execute([
        ':nome' => 'Você',
        ':usuario' => '@usuario',
        ':descricao' => $descricao,
        ':likes' => 0
    ]);

    $idInserido = $pdo->lastInsertId();

    echo json_encode([
        'status' => 'success',
        'message' => 'Sugestão cadastrada com sucesso!',
        'dados' => [
            'id' => $idInserido,
            'descricao' => $descricao
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao salvar no banco: ' . $e->getMessage()
    ]);
}
?>