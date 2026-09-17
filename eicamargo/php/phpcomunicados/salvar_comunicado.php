<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../sessao.php';
include_once '../conexao.php';

try {
    if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'coordenador') {
        echo json_encode(['status' => 'error', 'message' => 'Acesso negado. Apenas coordenadores podem publicar.']);
        exit;
    }

    $tipoComunicado = $_POST['tipo_comunicado'] ?? 'geral'; // 'geral' ou 'curso'
    $titulo          = $_POST['titulo'] ?? null;
    $conteudo        = $_POST['conteudo'] ?? null;
    $usuarioId       = $_SESSION['usuario_id'] ?? null;
    $cursoId         = $_SESSION['curso_id'] ?? ($_SESSION['usuario']['curso_id'] ?? null);

    if (empty($titulo) || empty($conteudo)) {
        echo json_encode(['status' => 'error', 'message' => 'Preencha o título e o conteúdo.']);
        exit;
    }

    // Se for comunicado do curso, vincula ao curso_id. Se for geral, força NULL.
    $cursoIdFinal = ($tipoComunicado === 'curso') ? $cursoId : null;

    $stmt = $pdo->prepare("
        INSERT INTO comunicados (usuario_id, curso_id, titulo, conteudo, data_criacao) 
        VALUES (:usuario_id, :curso_id, :titulo, :conteudo, NOW())
    ");

    $stmt->execute([
        ':usuario_id' => $usuarioId,
        ':curso_id'   => $cursoIdFinal,
        ':titulo'     => $titulo,
        ':conteudo'   => $conteudo
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Comunicado publicado com sucesso!']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar: ' . $e->getMessage()]);
}
?>