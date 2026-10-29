<?php
session_start();

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    include_once __DIR__ . '/../conexao.php';

    $meuId = $_SESSION['usuario']['id'] ?? ($_SESSION['usuario_id'] ?? null);
    $busca = trim($_GET['busca'] ?? '');

    if (!$meuId) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão expirada.']);
        exit;
    }

    if ($busca === '') {
        echo json_encode(['status' => 'success', 'usuarios' => []]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT id, nome, foto_perfil
        FROM usuarios
        WHERE id != :meuId AND nome LIKE :busca
        ORDER BY nome ASC
        LIMIT 20
    ");
    $stmt->execute([
        ':meuId' => $meuId,
        ':busca' => '%' . $busca . '%'
    ]);

    $usuarios = array_map(function ($u) {
        return [
            'usuario_id'  => (int)$u['id'],
            'nome'        => $u['nome'],
            'foto_perfil' => $u['foto_perfil']
        ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));

    echo json_encode(['status' => 'success', 'usuarios' => $usuarios]);

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>