<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

include_once '../conexao.php';

try {
    // Pega o ID do usuário que está logado na sessão
    $usuarioLogadoId = $_SESSION['usuario']['id'] ?? 0;

    $sql = "SELECT s.id, s.descricao, s.likes, s.usuario_id, 
                   u.nome, u.foto_perfil 
            FROM sugestoes s 
            JOIN usuarios u ON s.usuario_id = u.id 
            ORDER BY s.id DESC";
            
    $stmt = $pdo->query($sql);
    $sugestoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($sugestoes as &$sugestao) {
        // Incluído usuario_id na busca dos comentários
        $stmtC = $pdo->query("SELECT id, usuario_id, comentario, nome FROM comentarios WHERE sugestao_id = " . (int)$sugestao['id'] . " ORDER BY id ASC");
        $comentarios = $stmtC->fetchAll(PDO::FETCH_ASSOC);
    
        // Gera a userTag para cada comentário
        foreach ($comentarios as &$c) {
            $nomeComent = $c['nome'] ?? 'Usuário';
            $c['userTag'] = '@' . strtolower(str_replace(' ', '', $nomeComent));
        }
        unset($c);
    
        $sugestao['comentarios'] = $comentarios;
        $sugestao['usuario'] = '@' . strtolower(str_replace(' ', '', $sugestao['nome']));
    
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
    unset($sugestao);

    // Retorna as sugestões e o ID do usuário logado
    echo json_encode([
        'status' => 'success',
        'usuario_logado_id' => (int)$usuarioLogadoId,
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