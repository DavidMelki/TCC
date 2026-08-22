<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se não estiver logado, redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php"); // Altere para o caminho correto do seu login
    exit;
}

// Caminho corrigido para achar o conexao.php a partir da pasta php/
include_once __DIR__ . '/conexao.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $usuarioLogado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioLogado) {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
} catch (Exception $e) {
    $usuarioLogado = $_SESSION['usuario'] ?? [];
}
?>