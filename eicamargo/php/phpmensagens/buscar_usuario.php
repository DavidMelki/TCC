<?php
session_start();

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    include_once __DIR__ . '/../conexao.php';

    $meuId = $_SESSION['usuario']['id'] ?? null;
    $id    = (int)($_GET['usuario_id'] ?? 0);

    if (!$meuId) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão expirada.']);
        exit;
    }

    if ($id <= 0 || $id === (int)$meuId) {
        echo json_encode(['status' => 'error', 'message' => 'Usuário inválido.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, nome, foto_perfil FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$u) {
        echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado.']);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'usuario' => [
            'usuario_id'  => (int)$u['id'],
            'nome'        => $u['nome'],
            'foto_perfil' => $u['foto_perfil']
        ]
    ]);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>