<?php
// Garante que qualquer erro seja capturado em formato JSON e evita telas brancas
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$dbname = 'eicamargo'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Busca todas as sugestões
    $stmt = $pdo->prepare("SELECT id, nome, usuario, descricao, likes FROM sugestoes ORDER BY id DESC");
    $stmt->execute();
    $sugestoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Garante que o array não seja nulo
    if (!$sugestoes) {
        $sugestoes = [];
    }

    // Busca os comentários para cada sugestão de forma isolada
    foreach ($sugestoes as &$sugestao) {
        try {
            $stmtComentarios = $pdo->prepare("SELECT nome, comentario, criado_em FROM comentarios WHERE sugestao_id = :sugestao_id ORDER BY id ASC");
            $stmtComentarios->execute([':sugestao_id' => $sugestao['id']]);
            $sugestao['comentarios'] = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            $sugestao['comentarios'] = [];
        }
    }
    unset($sugestao);

    echo json_encode([
        'status' => 'success',
        'sugestoes' => $sugestoes
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao listar: ' . $e->getMessage(),
        'sugestoes' => []
    ]);
}
?>