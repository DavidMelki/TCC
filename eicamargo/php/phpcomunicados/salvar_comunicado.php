<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../sessao.php';
include_once '../conexao.php';

// Quem pode publicar cada tipo de comunicado
$tiposPublicamGeral = ['coordenador', 'diretor'];
$tiposPublicamCurso = ['coordenador', 'diretor', 'professor'];

// Quem pode escolher QUALQUER curso ao publicar (os demais publicam só no curso deles)
$tiposEscolhemCurso = ['diretor', 'professor'];

try {
    $tipoUsuario    = $_SESSION['tipo_usuario'] ?? null;
    $tipoComunicado = ($_POST['tipo_comunicado'] ?? 'geral') === 'curso' ? 'curso' : 'geral';
    $titulo         = trim($_POST['titulo'] ?? '');
    $conteudo       = trim($_POST['conteudo'] ?? '');
    $usuarioId      = $_SESSION['usuario_id'] ?? null;
    $cursoId        = $_SESSION['curso_id'] ?? ($_SESSION['usuario']['curso_id'] ?? null);

    $permitidos = ($tipoComunicado === 'curso') ? $tiposPublicamCurso : $tiposPublicamGeral;

    if (!in_array($tipoUsuario, $permitidos, true)) {
        $mensagem = ($tipoComunicado === 'curso')
            ? 'Acesso negado. Apenas coordenadores, diretores e professores podem publicar comunicados do curso.'
            : 'Acesso negado. Apenas coordenadores e diretores podem publicar comunicados gerais.';
        echo json_encode(['status' => 'error', 'message' => $mensagem]);
        exit;
    }

    if (empty($titulo) || empty($conteudo)) {
        echo json_encode(['status' => 'error', 'message' => 'Preencha o título e o conteúdo.']);
        exit;
    }

    // Diretor e professor podem escolher em qual curso publicar.
    // O coordenador sempre publica no curso dele (o curso_id enviado é ignorado).
    if ($tipoComunicado === 'curso' && in_array($tipoUsuario, $tiposEscolhemCurso, true)) {
        $cursoEscolhido = (int)($_POST['curso_id'] ?? 0);

        if ($cursoEscolhido > 0) {
            $stmtCurso = $pdo->prepare("SELECT id FROM cursos WHERE id = :id");
            $stmtCurso->execute([':id' => $cursoEscolhido]);

            if (!$stmtCurso->fetch()) {
                echo json_encode(['status' => 'error', 'message' => 'Curso inválido.']);
                exit;
            }

            $cursoId = $cursoEscolhido;
        }
    }

    // Comunicado do curso precisa de um curso.
    // Sem isso ele seria salvo com curso_id NULL e apareceria como comunicado GERAL.
    if ($tipoComunicado === 'curso' && empty($cursoId)) {
        echo json_encode(['status' => 'error', 'message' => 'Selecione o curso do comunicado.']);
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