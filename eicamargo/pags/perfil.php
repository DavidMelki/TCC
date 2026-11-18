<?php
// Garante login e carrega $pdo e $usuarioLogado (usuário que está navegando)
include '../php/sessao.php';

$meuId = (int) $usuarioLogado['id'];

// perfil.php            -> meu perfil 
// perfil.php?id=16      -> perfil de outro usuário (somente leitura)
$perfilId = (int) ($_GET['id'] ?? $meuId);
if ($perfilId <= 0) {
    $perfilId = $meuId;
}
$eMeuPerfil = ($perfilId === $meuId);

try {
    if ($eMeuPerfil) {
        $usuario = $usuarioLogado;
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $perfilId]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Usuário inexistente: volta para o próprio perfil
        if (!$usuario) {
            header('Location: perfil.php');
            exit;
        }
    }

    // Sugestões do usuário do perfil
    $stmtSugestoes = $pdo->prepare("SELECT * FROM sugestoes WHERE usuario_id = :usuario_id ORDER BY id DESC");
    $stmtSugestoes->execute([':usuario_id' => $perfilId]);
    $minhasSugestoes = $stmtSugestoes->fetchAll(PDO::FETCH_ASSOC);

    // Produtos à venda do usuário do perfil
    $stmtVendas = $pdo->prepare("SELECT * FROM vendas WHERE usuario_id = :usuario_id ORDER BY id DESC");
    $stmtVendas->execute([':usuario_id' => $perfilId]);
    $minhasVendas = $stmtVendas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}

// Imagens do perfil exibido
$fotoPerfil = !empty($usuario['foto_perfil']) ? '../uploads/' . htmlspecialchars($usuario['foto_perfil']) : '../uploads/default.png';
$fotoCapa = !empty($usuario['foto_capa']) ? '../uploads/' . htmlspecialchars($usuario['foto_capa']) : '';

// Foto de quem está logado (rodapé do menu lateral)
$fotoLogado = !empty($usuarioLogado['foto_perfil']) ? '../uploads/' . htmlspecialchars($usuarioLogado['foto_perfil']) : '../uploads/default.png';

$tagUsuario = '@' . strtolower(str_replace(' ', '', $usuario['nome']));
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eicamargo - Perfil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="icon" type="image/icon" href="../css/img/logoeicamargo.png">
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
                <li><a href="curso.php" class="nav-item"><i class="fa-solid fa-graduation-cap"></i> Curso</a></li>
                <li><a href="perfil.php" class="nav-item <?php echo $eMeuPerfil ? 'active' : ''; ?>"><i
                            class="fa-regular fa-user"></i> Perfil</a></li>
            </ul>

            <div class="profile-footer">
                <div class="profile-info">
                    <div class="avatar"
                        style="background-image: url('<?php echo $fotoLogado; ?>'); background-size: cover; background-position: center;">
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

        <!-- FEED CENTRAL -->
        <main class="feed">

            <!-- CAPA (BANNER) COM IMAGEM DINÂMICA -->
            <div class="capa"
                style="<?php echo !empty($fotoCapa) ? "background-image: url('$fotoCapa'); background-size: cover; background-position: center;" : ''; ?>">
            </div>

            <div class="perfil-header">
                <div class="foto-perfil"
                    style="background-image: url('<?php echo $fotoPerfil; ?>'); background-size: cover; background-position: center;">
                </div>

                <?php if ($eMeuPerfil): ?>
                    <button class="btn-editar">Editar Perfil</button>
                <?php else: ?>
                    <a href="mensagens.php?usuario_id=<?php echo (int) $usuario['id']; ?>"
                        style="display: inline-flex; align-items: center; gap: 6px; background: #e63c3c; color: #fff; text-decoration: none; font-size: 14px; font-weight: 600; padding: 9px 18px; border-radius: 20px; margin-left: auto; float: right; margin-top: 12px;">
                        <i class="fa-regular fa-envelope"></i> Enviar mensagem
                    </a>
                <?php endif; ?>
            </div>

            <?php $tipoPerfil = ($usuario['tipo'] ?? '') ?: 'aluno'; ?>
            <div class="info-perfil" data-tipo="<?php echo htmlspecialchars($tipoPerfil, ENT_QUOTES); ?>"
                data-ano="<?php echo (int) ($usuario['ano'] ?? 0); ?>"
                data-curso="<?php echo htmlspecialchars($usuario['curso'] ?? '', ENT_QUOTES); ?>"
                data-funcao="<?php echo htmlspecialchars($usuario['funcao'] ?? '', ENT_QUOTES); ?>"
                data-bio="<?php echo htmlspecialchars($usuario['biografia'] ?? '', ENT_QUOTES); ?>">
                <h2><?php echo htmlspecialchars($usuario['nome']); ?></h2>
                <span class="usuario"><?php echo htmlspecialchars($tagUsuario); ?></span>

                <?php if ($tipoPerfil === 'aluno'): ?>
                    <?php if (!empty($usuario['ano']) || !empty($usuario['curso'])): ?>
                        <p class="ano-curso" style="margin: 8px 0 0; font-size: 14px; font-weight: 600; color: #444;">
                            <i class="fa-solid fa-graduation-cap" style="color: #e63c3c; margin-right: 4px;"></i>
                            <?php
                            $partes = [];
                            if (!empty($usuario['ano']))
                                $partes[] = (int) $usuario['ano'] . 'º ano';
                            if (!empty($usuario['curso']))
                                $partes[] = htmlspecialchars($usuario['curso']);
                            echo implode(' • ', $partes);
                            ?>
                        </p>
                    <?php endif; ?>
                <?php elseif (!empty($usuario['funcao'])): ?>
                    <p class="ano-curso" style="margin: 8px 0 0; font-size: 14px; font-weight: 600; color: #444;">
                        <i class="fa-solid fa-briefcase" style="color: #e63c3c; margin-right: 4px;"></i>
                        <?php echo htmlspecialchars($usuario['funcao']); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty(trim($usuario['biografia'] ?? ''))): ?>
                    <p class="descricao"><?php echo htmlspecialchars($usuario['biografia']); ?></p>
                <?php endif; ?>
            </div>

            <!-- ABAS DIVIDIDAS -->
            <div class="abas">
                <div class="aba ativa" onclick="alternarAba('sugestoes', this)">
                    Sugestões (<?php echo count($minhasSugestoes); ?>)
                </div>
                <div class="aba" onclick="alternarAba('vendas', this)">
                    <?php echo $eMeuPerfil ? 'Meus Produtos' : 'Produtos'; ?> (<?php echo count($minhasVendas); ?>)
                </div>
            </div>

            <!-- CONTEÚDO 1: SUGESTÕES -->
            <div id="conteudo-sugestoes" class="lista-posts aba-conteudo">
                <?php if (empty($minhasSugestoes)): ?>
                    <p style="text-align: center; color: #8c8c8c; padding: 30px; font-size: 14px;">
                        <?php echo $eMeuPerfil ? 'Você ainda não enviou nenhuma sugestão.' : 'Este usuário ainda não enviou nenhuma sugestão.'; ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($minhasSugestoes as $sugestao): ?>
                        <div class="post">
                            <div class="avatar-post"
                                style="background-image: url('<?php echo $fotoPerfil; ?>'); background-size: cover; background-position: center;">
                            </div>
                            <div class="conteudo-post">
                                <div class="post-header">
                                    <h4><?php echo htmlspecialchars($usuario['nome']); ?></h4>
                                    <span><?php echo htmlspecialchars($tagUsuario); ?></span>
                                </div>
                                <p><?php echo htmlspecialchars($sugestao['texto'] ?? $sugestao['sugestao'] ?? $sugestao['descricao'] ?? ''); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- CONTEÚDO 2: PRODUTOS DE VENDA -->
            <div id="conteudo-vendas" class="lista-posts aba-conteudo" style="display: none;">
                <?php if (empty($minhasVendas)): ?>
                    <p style="text-align: center; color: #8c8c8c; padding: 30px; font-size: 14px;">
                        <?php echo $eMeuPerfil ? 'Você ainda não anunciou nenhum produto.' : 'Este usuário ainda não anunciou nenhum produto.'; ?>
                    </p>
                <?php else: ?>
                    <?php foreach ($minhasVendas as $venda): ?>
                        <div class="post">
                            <?php if (!empty($venda['midia'])): ?>
                                <img src="<?php echo htmlspecialchars($venda['midia']); ?>"
                                    style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">
                            <?php else: ?>
                                <div class="avatar-post"
                                    style="background-image: url('<?php echo $fotoPerfil; ?>'); background-size: cover; background-position: center;">
                                </div>
                            <?php endif; ?>
                            <div class="conteudo-post">
                                <div class="post-header">
                                    <h4><?php echo htmlspecialchars($venda['titulo']); ?></h4>
                                    <span
                                        style="color: #e63c3c; font-weight: bold;"><?php echo htmlspecialchars($venda['preco']); ?></span>
                                </div>
                                <p><?php echo htmlspecialchars($venda['legenda'] ?? ''); ?></p>
                                <span style="font-size: 12px; color: #8c8c8c;"><i class="fa-solid fa-location-dot"></i>
                                    <?php echo htmlspecialchars($venda['local'] ?? 'Não informado'); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </main>

        <!-- BARRA DIREITA -->
        <aside class="right-bar">
            <div class="card-info">
                <?php if ($eMeuPerfil): ?>
                    <h3>Seu Perfil</h3>
                    <p>Mantenha suas informações atualizadas para que outros alunos e professores reconheçam suas
                        publicações.</p>
                <?php else: ?>
                    <h3>Perfil de <?php echo htmlspecialchars(explode(' ', trim($usuario['nome']))[0]); ?></h3>
                    <p>Veja as sugestões e os produtos publicados por este usuário ou envie uma mensagem para conversar.</p>
                <?php endif; ?>
            </div>
            <p class="copyright">© 2026 Camargo Aranha.</p>
        </aside>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/jsperfil/alterarperfil.js?v=<?php echo time(); ?>"></script>
    <script>
        function alternarAba(aba, elemento) {
            document.querySelectorAll('.aba').forEach(el => el.classList.remove('ativa'));
            document.querySelectorAll('.aba-conteudo').forEach(el => el.style.display = 'none');

            elemento.classList.add('ativa');
            document.getElementById('conteudo-' + aba).style.display = 'block';
        }
    </script>

    <script src="../js/rodape-perfil.js"></script>
    <script src="../js/alterarperfil.js"></script>
</body>

</html>