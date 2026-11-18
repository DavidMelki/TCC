<?php
// Funcao central de permissao de moderacao.
// Consulta o banco a cada chamada (nao confia so na sessao).
function ehModerador(PDO $pdo, $usuarioId): bool
{
    if (!$usuarioId) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT tipo FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => (int)$usuarioId]);
    $tipo = $stmt->fetchColumn();

    return in_array($tipo, ['diretor', 'coordenador'], true);
}
?>