<?php
header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

try {
    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    if (empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Preencha o e-mail e a senha.']);
        exit;
    }

    // Busca o usuário pelo e-mail
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Se o usuário não existir ou a senha estiver errada
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        echo json_encode(['status' => 'error', 'message' => 'E-mail ou senha incorretos.']);
        exit;
    }

    // Inicia a sessão e guarda os dados essenciais
    session_start();
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    
    // IMPORTANTE: Salvamos o array completo ou a foto na sessão para o uso imediato
    $_SESSION['usuario'] = $usuario; 

    echo json_encode([
        'status' => 'success',
        'message' => 'Login realizado com sucesso!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro no servidor: ' . $e->getMessage()
    ]);
}
?>