<?php
session_start();
date_default_timezone_set('America_Sao_Paulo');

if (ob_get_length()) ob_clean();
header('Content-Type: application/json; charset=utf-8');
include_once '../conexao.php';

$usuarioLogadoId = $_SESSION['usuario']['id'] ?? null;
$termoBusca = trim($_GET['busca'] ?? '');

if (!$usuarioLogadoId) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
    exit;
}

try {
    $pdo->exec("SET time_zone = '-03:00'");

    if (!empty($termoBusca)) {
        $stmt = $pdo->prepare("
            SELECT id AS usuario_id, nome, foto_perfil 
            FROM usuarios 
            WHERE id != :meuId AND nome LIKE :busca
            LIMIT 20
        ");
        $stmt->execute([
            ':meuId' => $usuarioLogadoId,
            ':busca' => '%' . $termoBusca . '%'
        ]);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = array_map(function($u) {
            return [
                'usuario_id' => $u['usuario_id'],
                'nome' => $u['nome'],
                'foto_perfil' => $u['foto_perfil'],
                'ultima_msg' => 'Clique para iniciar uma conversa',
                'tempo' => '',
                'nao_lidas' => 0
            ];
        }, $usuarios);

    } else {
        $stmt = $pdo->prepare("
            SELECT 
                u.id AS usuario_id,
                u.nome,
                u.foto_perfil,
                m.mensagem AS ultima_msg,
                m.data_envio,
                (
                    SELECT COUNT(*) 
                    FROM mensagens 
                    WHERE remetente_id = u.id 
                      AND destinatario_id = :meuId 
                      AND lida = 0
                ) AS nao_lidas
            FROM usuarios u
            INNER JOIN mensagens m ON (
                (m.remetente_id = :meuId AND m.destinatario_id = u.id) OR
                (m.destinatario_id = :meuId AND m.remetente_id = u.id)
            )
            INNER JOIN (
                SELECT 
                    LEAST(remetente_id, destinatario_id) AS p1,
                    GREATEST(remetente_id, destinatario_id) AS p2,
                    MAX(id) AS max_id
                FROM mensagens
                WHERE remetente_id = :meuId OR destinatario_id = :meuId
                GROUP BY p1, p2
            ) conversa_recente ON m.id = conversa_recente.max_id
            ORDER BY m.data_envio DESC
        ");
        $stmt->execute([':meuId' => $usuarioLogadoId]);
        $conversas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($conversas)) {
            $stmt = $pdo->prepare("
                SELECT id AS usuario_id, nome, foto_perfil 
                FROM usuarios 
                WHERE id != :meuId
                ORDER BY nome ASC
                LIMIT 20
            ");
            $stmt->execute([':meuId' => $usuarioLogadoId]);
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado = array_map(function($u) {
                return [
                    'usuario_id' => $u['usuario_id'],
                    'nome' => $u['nome'],
                    'foto_perfil' => $u['foto_perfil'],
                    'ultima_msg' => 'Nova conversa — Clique para falar',
                    'tempo' => '',
                    'nao_lidas' => 0
                ];
            }, $usuarios);

        } else {
            $resultado = array_map(function($c) {
                $dt = new DateTime($c['data_envio']);
                $dt->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                return [
                    'usuario_id' => $c['usuario_id'],
                    'nome' => $c['nome'],
                    'foto_perfil' => $c['foto_perfil'],
                    'ultima_msg' => $c['ultima_msg'],
                    'tempo' => $dt->format('H:i'),
                    'nao_lidas' => (int)$c['nao_lidas']
                ];
            }, $conversas);
        }
    }

    echo json_encode(['status' => 'success', 'conversas' => $resultado]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>