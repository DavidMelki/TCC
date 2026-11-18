<?php
include '../php/sessao.php';
include '../php/conexao.php';

// Curso do usuário logado
$cursoIdUsuario = $_SESSION['curso_id'] ?? ($_SESSION['usuario']['curso_id'] ?? 0);

// DIRETOR e PROFESSOR podem ver os comunicados de qualquer curso (curso.php?curso_id=3).
// Os demais usuários sempre veem apenas o curso deles.
$podeEscolherCurso = in_array($_SESSION['tipo_usuario'] ?? '', ['diretor', 'professor'], true);
$cursoExibido = $cursoIdUsuario;
$listaCursos = [];

if ($podeEscolherCurso) {
    $linhasCursos = $pdo->query("SELECT * FROM cursos ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($linhasCursos as $r) {
        $listaCursos[] = [
            'id'   => (int)$r['id'],
            'nome' => $r['nome'] ?? $r['nome_curso'] ?? $r['titulo'] ?? $r['curso'] ?? ('Curso ' . $r['id'])
        ];
    }

    $pedido = (int)($_GET['curso_id'] ?? 0);
    if ($pedido > 0 && in_array($pedido, array_column($listaCursos, 'id'), true)) {
        $cursoExibido = $pedido;
    }

    // Professor não tem curso próprio: começa pelo primeiro curso da lista
    if (empty($cursoExibido) && !empty($listaCursos)) {
        $cursoExibido = $listaCursos[0]['id'];
    }
}

// Busca apenas comunicados VINCULADOS AO CURSO exibido
$stmt = $pdo->prepare("
    SELECT c.*, u.nome AS autor_nome, u.foto_perfil AS autor_foto
    FROM comunicados c
    INNER JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.curso_id = :curso_id
    ORDER BY c.data_criacao DESC
");
$stmt->execute([':curso_id' => $cursoExibido]);
$comunicadosCurso = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eicamargo - Curso</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../css/curso.css">
    <link rel="stylesheet" href="../css/sugestoes.css">
    <link rel="icon" type="image/icon" href="../css/img/logoeicamargo.png">
    <meta name="author" content="Alexandre Castello, David Melquiades, Bruna de Mello e Julia Akemi">
</head>

<body>

    <div class="app-container">

        <!-- MENU LATERAL -->
        <aside class="sidebar-left">
            <div class="logo">
                <img src="../css/img/logoeicamargo.png" alt="Logo">
            </div>

            <ul class="nav-menu">
                <li><a href="sugestoes.php" class="nav-item"><i class="bi bi-lightbulb-fill"></i> Sugestões</a></li>
                <li><a href="vendas.php" class="nav-item"><i class="fa-solid fa-cart-shopping"></i> Reservas</a></li>
                <li><a href="comunicados.php" class="nav-item"><i class="fa-solid fa-bullhorn"></i> Comunicados</a></li>
                <li><a href="mensagens.php" class="nav-item"><i class="fa-regular fa-envelope"></i> Mensagens</a></li>
                <li><a href="curso.php" class="nav-item active"><i class="fa-solid fa-graduation-cap"></i> Curso</a>
                </li>
                <li><a href="perfil.php" class="nav-item"><i class="fa-regular fa-user"></i> Perfil</a></li>
            </ul>

            <?php if (in_array($_SESSION['tipo_usuario'] ?? '', ['coordenador', 'diretor', 'professor'], true)): ?>
                <button class="btn-nova-sugestao" id="btnNovoComunicadoCurso" style="margin-top: 20px;">
                    <i class="bi bi-plus-lg"></i> Comunicado Curso
                </button>
            <?php endif; ?>

            <div class="profile-footer">
                <div class="profile-info">
                    <div class="avatar"
                        style="width: 40px; height: 40px; border-radius: 50%; background-image: url('../uploads/<?php echo !empty($usuarioLogado['foto_perfil']) ? htmlspecialchars($usuarioLogado['foto_perfil']) : 'default.png'; ?>'); background-size: cover; background-position: center;">
                    </div>
                    <div class="dados">
                        <span class="nome"><?php echo htmlspecialchars($usuarioLogado['nome']); ?></span>
                        <span
                            class="usuario">@<?php echo strtolower(str_replace(' ', '', $usuarioLogado['nome'])); ?></span>
                    </div>
                </div>
                <i class="bi bi-three-dots"></i>
            </div>
        </aside>

        <!-- FEED CENTRAL (APENAS COMUNICADOS DO CURSO) -->
        <main class="feed">

            <div class="top-feed" style="display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                <h2>Comunicados do Curso</h2>

                <?php if ($podeEscolherCurso && !empty($listaCursos)): ?>
                    <!-- Só o diretor e o professor veem este seletor: escolhem qual curso querem ver -->
                    <select onchange="window.location.href='curso.php?curso_id=' + this.value"
                        style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: #fff; max-width: 280px;">
                        <?php foreach ($listaCursos as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ((int)$c['id'] === (int)$cursoExibido) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
            </div>

            <div class="lista-posts">
                <?php $listaExibicao = $comunicadosCurso ?? []; ?>

                <?php if (empty($listaExibicao)): ?>
                    <p class="sem-comunicados">Nenhum comunicado do seu curso no momento.</p>
                <?php else: ?>
                    <?php foreach ($listaExibicao as $item): ?>
                        <div class="card-comunicado">
                            <div class="card-comunicado-header">
                                <h3 class="card-comunicado-titulo"><?php echo htmlspecialchars($item['titulo']); ?></h3>
                                <span class="card-comunicado-data">
                                    <?php echo date('d/m/Y \à\s H:i', strtotime($item['data_criacao'])); ?>
                                </span>
                            </div>

                            <p class="card-comunicado-texto">
                                <?php echo nl2br(htmlspecialchars($item['conteudo'])); ?>
                            </p>

                            <div class="card-comunicado-footer">
                                <!-- Clicar no autor abre o perfil dele -->
                                <a class="autor-info" href="perfil.php?id=<?php echo (int)$item['usuario_id']; ?>"
                                    title="Ver perfil" style="text-decoration: none; color: inherit;">
                                    <span class="avatar-mini"
                                        style="background-image: url('../uploads/<?php echo !empty($item['autor_foto']) ? htmlspecialchars($item['autor_foto']) : 'default.png'; ?>'); background-size: cover; background-position: center;"></span>
                                    <span
                                        class="autor-nome"><?php echo htmlspecialchars($item['autor_nome'] ?? $item['nome'] ?? 'Coordenação'); ?></span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </main>

        <aside class="right-bar">
            <div class="card-info">
                <h3>Avisos do seu Curso</h3>
                <p>Aqui você encontra avisos específicos do seu curso técnico, horários de aula, provas e comunicados
                    dos professores.</p>
            </div>
            <p class="copyright">© 2026 Camargo Aranha.</p>
        </aside>

    </div>

    <script src="../js/jscomunicados/curso.js?v=<?php echo time(); ?>"></script>
    <script src="../js/rodape-perfil.js"></script>
</body>

</html>