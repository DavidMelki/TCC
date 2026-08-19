<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$dbname = 'eicamargo'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Busca todas as sugestões
    $stmt = $pdo->query("SELECT id, nome, usuario, descricao, likes FROM sugestoes ORDER BY id DESC");
    $sugestoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Para cada sugestão, busca os seus respectivos comentários e ajusta o liked
    foreach ($sugestoes as &$sugestao) {
        $stmtC = $pdo->prepare("SELECT id, nome, comentario FROM comentarios WHERE sugestao_id = :sugestao_id ORDER BY id ASC");
        $stmtC->execute([':sugestao_id' => $sugestao['id']]);
        $sugestao['comentarios'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);

        // Garante que o número de likes seja um número inteiro
        $sugestao['likes'] = (int)($sugestao['likes'] ?? 0);

        // Define 'liked' como true se os likes forem maior que 0 (ou você pode ajustar conforme sua lógica de sessão de usuário)
        // Se você tiver uma tabela de curtidas por usuário, o ideal é checar se o ID do usuário logado curtiu aqui.
        $sugestao['liked'] = ($sugestao['likes'] > 0); 
    }
    unset($sugestao);

    echo json_encode([
        'status' => 'success',
        'sugestoes' => $sugestoes
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'sugestoes' => []
    ]);
}
?>