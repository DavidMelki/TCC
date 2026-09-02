<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

try {
    $sql = "SELECT s.id, s.descricao, s.likes, s.usuario_id, 
                   u.nome, u.foto_perfil 
            FROM sugestoes s 
            JOIN usuarios u ON s.usuario_id = u.id 
            ORDER BY s.id DESC";
            
    $stmt = $pdo->query($sql);
    $sugestoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($sugestoes as &$sugestao) {
        $stmtC = $pdo->query("SELECT id, nome, comentario FROM comentarios WHERE sugestao_id = " . (int)$sugestao['id'] . " ORDER BY id ASC");
        $sugestao['comentarios'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);

        $sugestao['usuario'] = '@' . strtolower(str_replace(' ', '', $sugestao['nome']));

        // Ajusta apenas o nome do arquivo da foto
        $foto = trim($sugestao['foto_perfil'] ?? '');
        if (!empty($foto)) {
            $fotoCorrigida = str_replace([' ', ' png'], ['.', '.png'], $foto);
            $sugestao['foto_perfil'] = $fotoCorrigida;
        } else {
            $sugestao['foto_perfil'] = '';
        }

        $sugestao['likes'] = (int)($sugestao['likes'] ?? 0);
        $sugestao['liked'] = ($sugestao['likes'] > 0); 
    }
    unset($sugestao); // Fechado corretamente fora do foreach

    // ENVIA OS DADOS COM SUCESSO PARA O JS:
    echo json_encode([
        'status' => 'success',
        'sugestoes' => $sugestoes
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'sugestoes' => []
    ]);
}
?>