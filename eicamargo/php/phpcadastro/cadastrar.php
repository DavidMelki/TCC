<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php'; 

// Domínio de e-mail permitido para cada tipo de usuário
$dominiosPorTipo = [
    'aluno'       => ['@aluno.cps.sp.gov.br'],
    'coordenador' => ['@cps.sp.gov.br'],
    'professor'   => ['@cps.sp.gov.br'],
    'diretor'     => ['@cps.sp.gov.br'],
];

try {
    $nome   = trim($_POST['nome'] ?? '');
    $email  = strtolower(trim($_POST['email'] ?? ''));
    $senha  = $_POST['senha']  ?? null;
    $tipo   = $_POST['tipo']   ?? 'aluno';
    $codigo = $_POST['codigo'] ?? null; // Código do curso fornecido pelo usuário

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos obrigatórios.']);
        exit;
    }

    // 0. O tipo de usuário precisa ser um dos permitidos
    if (!isset($dominiosPorTipo[$tipo])) {
        echo json_encode(['status' => 'error', 'message' => 'Tipo de acesso inválido.']);
        exit;
    }

    // 0.1 Valida o formato e o domínio do e-mail conforme o tipo
    $dominioValido = false;
    foreach ($dominiosPorTipo[$tipo] as $dominio) {
        if (str_ends_with($email, $dominio)) {
            $dominioValido = true;
            break;
        }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$dominioValido) {
        $mensagemDominio = ($tipo === 'aluno')
            ? 'Alunos devem usar o e-mail institucional (@aluno.cps.sp.gov.br).'
            : 'Use o seu e-mail institucional (@cps.sp.gov.br) para se cadastrar.';

        echo json_encode(['status' => 'error', 'message' => $mensagemDominio]);
        exit;
    }

    // 1. Verifica se o e-mail já existe
    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmtCheck->execute([':email' => $email]);
    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Este e-mail já está cadastrado.']);
        exit;
    }

    // 2. Valida o código do curso e obtém o curso_id
    //    Aluno usa o código do aluno; os demais usam o código de coordenador
    $cursoId = null;
    if (!empty($codigo)) {
        if ($tipo === 'aluno') {
            $stmtCurso = $pdo->prepare("SELECT id FROM cursos WHERE codigo_aluno = :codigo");
        } else {
            $stmtCurso = $pdo->prepare("SELECT id FROM cursos WHERE codigo_coordenador = :codigo");
        }
        
        $stmtCurso->execute([':codigo' => $codigo]);
        $curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);

        if (!$curso) {
            echo json_encode(['status' => 'error', 'message' => 'Código do curso inválido.']);
            exit;
        }

        $cursoId = $curso['id'];
    }

    // 3. Criptografa a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // 4. Insere no banco com o 'tipo' e o 'curso_id'
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nome, email, senha, tipo, curso_id) 
        VALUES (:nome, :email, :senha, :tipo, :curso_id)
    ");
    $stmt->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':senha'    => $senhaHash,
        ':tipo'     => $tipo,
        ':curso_id' => $cursoId
    ]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Cadastro realizado com sucesso!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Erro ao cadastrar: ' . $e->getMessage()
    ]);
}
?>