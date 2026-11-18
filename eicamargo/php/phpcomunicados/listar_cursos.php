<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../sessao.php';
include_once '../conexao.php';

try {
    // Somente o diretor e o professor podem escolher o curso do comunicado
    if (!in_array($_SESSION['tipo_usuario'] ?? '', ['diretor', 'professor'], true)) {
        echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
        exit;
    }

    // SELECT * para funcionar com qualquer nome de coluna do nome do curso
    $linhas = $pdo->query("SELECT * FROM cursos ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

    $cursos = [];
    foreach ($linhas as $c) {
        $cursos[] = [
            'id'   => (int)$c['id'],
            'nome' => $c['nome'] ?? $c['nome_curso'] ?? $c['titulo'] ?? $c['curso'] ?? ('Curso ' . $c['id'])
        ];
    }

    echo json_encode([
        'status'       => 'success',
        'cursos'       => $cursos,
        'curso_padrao' => (int)($_SESSION['curso_id'] ?? 0)
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>