<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php'; 

try {
    $nome   = $_POST['nome']   ?? null;
    $email  = $_POST['email']  ?? null;
    $senha  = $_POST['senha']  ?? null;
    $tipo   = $_POST['tipo']   ?? 'aluno'; // 'aluno' ou 'coordenador'
    $codigo = $_POST['codigo'] ?? null; // Código do curso fornecido pelo usuário

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos obrigatórios.']);
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
    $cursoId = null;
    if (!empty($codigo)) {
        if ($tipo === 'coordenador') {
            $stmtCurso = $pdo->prepare("SELECT id FROM cursos WHERE codigo_coordenador = :codigo");
        } else {
            $stmtCurso = $pdo->prepare("SELECT id FROM cursos WHERE codigo_aluno = :codigo");
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