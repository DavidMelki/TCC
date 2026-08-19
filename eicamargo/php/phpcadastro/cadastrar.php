<?php
header('Content-Type: application/json; charset=utf-8');

// Inclui a conexão que você já tem na pasta php
include_once '../conexao.php'; 

try {
    // Recebe os dados enviados via POST
    $nome = $_POST['nome'] ?? null;
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Preencha todos os campos.']);
        exit;
    }

    // Verifica se o e-mail já existe
    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmtCheck->execute([':email' => $email]);
    if ($stmtCheck->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Este e-mail já está cadastrado.']);
        exit;
    }

    // Criptografa a senha por segurança
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Insere no banco
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senhaHash
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Cadastro realizado com sucesso!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro ao cadastrar: ' . $e->getMessage()
    ]);
}
?>