<?php
// Evita qualquer eco de erro em HTML que possa quebrar o JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$dbname = 'eicamargo'; 
$username = 'root';
$password = '';

// Pega o ID do usuário da sessão (ajuste se a sua variável de sessão tiver outro nome)
$usuario_id = $_SESSION['usuario_id'] ?? 1; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nome = $_POST['nome'] ?? '';
    $biografia = $_POST['biografia'] ?? '';
    
    $fotoPerfilSql = "";
    $parametros = [
        ':nome' => $nome,
        ':biografia' => $biografia,
        ':id' => $usuario_id
    ];

    // Verifica se uma foto foi enviada sem erros
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novoNomeFoto = "perfil_" . $usuario_id . "_" . time() . "." . $extensao;
        
        // Caminho da pasta uploads na raiz do projeto (ajuste caso sua pasta uploads esteja em outro lugar)
        $pastaDestino = "../../uploads/"; 
        
        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0777, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $pastaDestino . $novoNomeFoto)) {
            $fotoPerfilSql = ", foto_perfil = :foto";
            $parametros[':foto'] = $novoNomeFoto;
        }
    }

    // Atualiza nome, biografia e foto na tabela usuarios
    $sql = "UPDATE usuarios SET nome = :nome, biografia = :biografia $fotoPerfilSql WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parametros);

    echo json_encode(['status' => 'success', 'message' => 'Perfil atualizado com sucesso!']);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>