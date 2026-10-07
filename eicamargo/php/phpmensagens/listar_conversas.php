<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    $caminhoConexao = __DIR__ . '/../conexao.php';
    if (!file_exists($caminhoConexao)) {
        throw new Exception("Arquivo de conexão não encontrado.");
    }
    include_once $caminhoConexao;

    $pdo->exec("SET time_zone = '-03:00'");

    $usuarioLogadoId = $_SESSION['usuario']['id'] ?? null;

    if (!$usuarioLogadoId) {
        echo json_encode(['status' => 'error', 'message' => 'Sessão expirada.']);
        exit;
    }

    $busca = trim($_GET['busca'] ?? '');
    $filtro = trim($_GET['filtro'] ?? 'todas');

    // Traz apenas usuários que têm pelo menos 1 mensagem com o usuário logado
    $sql = "
        SELECT 
            u.id AS usuario_id,
            u.nome,
            u.foto_perfil,
            (
                SELECT mensagem 
                FROM mensagens m 
                WHERE (m.remetente_id = u.id AND m.destinatario_id = :meuId1)
                   OR (m.remetente_id = :meuId2 AND m.destinatario_id = u.id)
                ORDER BY m.data_envio DESC LIMIT 1
            ) AS ultima_msg,
            (
                SELECT COUNT(*) 
                FROM mensagens m 
                WHERE m.remetente_id = u.id 
                  AND m.destinatario_id = :meuId3 
                  AND m.lida = 0
            ) AS nao_lidas,
            (
                SELECT MAX(data_envio) 
                FROM mensagens m 
                WHERE (m.remetente_id = u.id AND m.destinatario_id = :meuId4)
                   OR (m.remetente_id = :meuId5 AND m.destinatario_id = u.id)
            ) AS ultima_data
        FROM usuarios u
        WHERE u.id != :meuId6
          AND EXISTS (
              SELECT 1 FROM mensagens m 
              WHERE (m.remetente_id = u.id AND m.destinatario_id = :meuId7)
                 OR (m.remetente_id = :meuId8 AND m.destinatario_id = u.id)
          )
    ";

    $params = [
        ':meuId1' => $usuarioLogadoId,
        ':meuId2' => $usuarioLogadoId,
        ':meuId3' => $usuarioLogadoId,
        ':meuId4' => $usuarioLogadoId,
        ':meuId5' => $usuarioLogadoId,
        ':meuId6' => $usuarioLogadoId,
        ':meuId7' => $usuarioLogadoId,
        ':meuId8' => $usuarioLogadoId
    ];

    // Se o usuário digitou algo na busca por nome
    if (!empty($busca)) {
        $sql .= " AND u.nome LIKE :busca";
        $params[':busca'] = '%' . $busca . '%';
    }

    // Se o filtro selecionado for "Não lidas"
    if ($filtro === 'nao_lidas') {
        $sql .= " HAVING nao_lidas > 0";
    }

    // Ordena mostrando a mensagem mais recente primeiro
    $sql .= " ORDER BY ultima_data DESC, u.nome ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $conversasBrutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $conversas = array_map(function($c) {
        $tempo = '';
        if (!empty($c['ultima_data'])) {
            $dt = new DateTime($c['ultima_data']);
            $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
            $hoje = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));

            if ($dt->format('Y-m-d') === $hoje->format('Y-m-d')) {
                $tempo = $dt->format('H:i');
            } else {
                $tempo = $dt->format('d/m');
            }
        }

        return [
            'usuario_id' => (int)$c['usuario_id'],
            'nome' => $c['nome'],
            'foto_perfil' => $c['foto_perfil'],
            'ultima_msg' => strlen($c['ultima_msg']) > 30 ? mb_substr($c['ultima_msg'], 0, 30) . '...' : $c['ultima_msg'],
            'nao_lidas' => (int)$c['nao_lidas'],
            'tempo' => $tempo
        ];
    }, $conversasBrutas);

    echo json_encode([
        'status' => 'success',
        'conversas' => $conversas
    ]);

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>