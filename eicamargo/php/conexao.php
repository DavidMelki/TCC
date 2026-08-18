<?php
// Use 'localhost' em vez de '127.0.0.1' para alinhar com o XAMPP
$host = 'localhost';
$dbname = 'eicamargo'; // Nome exato exibido na sua imagem
$username = 'root';
$password = '';        // Deixe vazio (padrão do XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro na conexão: ' . $e->getMessage()
    ]);
    exit;
}
?>