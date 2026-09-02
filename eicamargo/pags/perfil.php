<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$host = 'localhost';
$dbname = 'eicamargo';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Busca dados do usuário
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    // 2. Busca as Sugestões do usuário
    $stmtSugestoes = $pdo->prepare("SELECT * FROM sugestoes WHERE usuario_id = :usuario_id ORDER BY id DESC");
    $stmtSugestoes->execute([':usuario_id' => $_SESSION['usuario_id']]);
    $minhasSugestoes = $stmtSugestoes->fetchAll(PDO::FETCH_ASSOC);

    // 3. Busca os Produtos cadastrados para Venda do usuário
    $stmtVendas = $pdo->prepare("SELECT * FROM vendas WHERE usuario_id = :usuario_id ORDER BY id DESC");
    $stmtVendas->execute([':usuario_id' => $_SESSION['usuario_id']]);
    $minhasVendas = $stmtVendas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}

// Define os caminhos das imagens
$fotoPerfil = !empty($usuario['foto_perfil']) ? '../uploads/' . htmlspecialchars($usuario['foto_perfil']) : '../uploads/default.png';
$fotoCapa = !empty($usuario['foto_capa']) ? '../uploads/' . htmlspecialchars($usuario['foto_capa']) : '';
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
                <li><a href="vendas.php" class="nav-item"><i class="fa-solid fa-cart-shopping"></i> Vendas</a></li>
                <li><a href="comunicados.php" class="nav-item"><i class="fa-solid fa-bullhorn"></i> Comunicados</a></li>
                <li><a href="mensagens.php" class="nav-item"><i class="fa-regular fa-envelope"></i> Mensagens</a></li>
                <li><a href="curso.php" class="nav-item"><i class="fa-solid fa-graduation-cap"></i> Curso</a></li>
                <li><a href="perfil.php" class="nav-item active"><i class="fa-regular fa-user"></i> Perfil</a></li>
            </ul>

            <div class="profile-footer">
                <div class="profile-info">
                    <div class="avatar" style="background-image: url('<?php echo $fotoPerfil; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="dados">
                        <span class="nome"><?php echo htmlspecialchars($usuario['nome']); ?></span>
                        <span class="usuario">@<?php echo strtolower(str_replace(' ', '', $usuario['nome'])); ?></span>
                    </div>
                </div>
                <i class="bi bi-three-dots"></i>
            </div>
        </aside>

        <!-- FEED CENTRAL -->
        <main class="feed">

            <!-- CAPA (BANNER) COM IMAGEM DINÂMICA -->
            <div class="capa" style="<?php echo !empty($fotoCapa) ? "background-image: url('$fotoCapa'); background-size: cover; background-position: center;" : ''; ?>"></div>

            <div class="perfil-header">
                <div class="foto-perfil" style="background-image: url('<?php echo $fotoPerfil; ?>'); background-size: cover; background-position: center;"></div>
                <button class="btn-editar">Editar Perfil</button>
            </div>

            <div class="info-perfil">
                <h2><?php echo htmlspecialchars($usuario['nome']); ?></h2>
                <span class="usuario">@<?php echo strtolower(str_replace(' ', '', $usuario['nome'])); ?></span>
                <p class="descricao">
                    <?php echo htmlspecialchars($usuario['biografia'] ?? 'Nenhuma biografia informada.'); ?>
                </p>
            </div>

            <!-- ABAS DIVIDIDAS -->
            <div class="abas">
                <div class="aba ativa" onclick="alternarAba('sugestoes', this)">
                    Sugestões (<?php echo count($minhasSugestoes); ?>)
                </div>
                <div class="aba" onclick="alternarAba('vendas', this)">
                    Meus Produtos (<?php echo count($minhasVendas); ?>)
                </div>
            </div>

            <!-- CONTEÚDO 1: SUGESTÕES -->
            <div id="conteudo-sugestoes" class="lista-posts aba-conteudo">
                <?php if (empty($minhasSugestoes)): ?>
                    <p style="text-align: center; color: #8c8c8c; padding: 30px; font-size: 14px;">Você ainda não enviou nenhuma sugestão.</p>
                <?php else: ?>
                    <?php foreach ($minhasSugestoes as $sugestao): ?>
                        <div class="post">
                            <div class="avatar-post" style="background-image: url('<?php echo $fotoPerfil; ?>'); background-size: cover; background-position: center;"></div>
                            <div class="conteudo-post">
                                <div class="post-header">
                                    <h4><?php echo htmlspecialchars($usuario['nome']); ?></h4>
                                    <span>@<?php echo strtolower(str_replace(' ', '', $usuario['nome'])); ?></span>
                                </div>
                                <p><?php echo htmlspecialchars($sugestao['texto'] ?? $sugestao['sugestao'] ?? $sugestao['descricao'] ?? ''); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- CONTEÚDO 2: PRODUTOS DE VENDA -->
            <div id="conteudo-vendas" class="lista-posts aba-conteudo" style="display: none;">
                <?php if (empty($minhasVendas)): ?>
                    <p style="text-align: center; color: #8c8c8c; padding: 30px; font-size: 14px;">Você ainda não anunciou nenhum produto.</p>
                <?php else: ?>
                    <?php foreach ($minhasVendas as $venda): ?>
                        <div class="post">
                            <?php if (!empty($venda['midia'])): ?>
                                <img src="<?php echo htmlspecialchars($venda['midia']); ?>" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">
                            <?php else: ?>
                                <div class="avatar-post" style="background-image: url('<?php echo $fotoPerfil; ?>'); background-size: cover; background-position: center;"></div>
                            <?php endif; ?>
                            <div class="conteudo-post">
                                <div class="post-header">
                                    <h4><?php echo htmlspecialchars($venda['titulo']); ?></h4>
                                    <span style="color: #e63c3c; font-weight: bold;"><?php echo htmlspecialchars($venda['preco']); ?></span>
                                </div>
                                <p><?php echo htmlspecialchars($venda['legenda'] ?? ''); ?></p>
                                <span style="font-size: 12px; color: #8c8c8c;"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($venda['local'] ?? 'Não informado'); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </main>

        <!-- BARRA DIREITA -->
        <aside class="right-bar">
            <div class="card-info">
                <h3>Seu Perfil</h3>
                <p>Mantenha suas informações atualizadas para que outros alunos e professores reconheçam suas publicações.</p>
            </div>
            <p class="copyright">© 2026 Camargo Aranha.</p>
        </aside>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/jsperfil/alterarperfil.js"></script>
    <script>
        function alternarAba(aba, elemento) {
            document.querySelectorAll('.aba').forEach(el => el.classList.remove('ativa'));
            document.querySelectorAll('.aba-conteudo').forEach(el => el.style.display = 'none');

            elemento.classList.add('ativa');
            document.getElementById('conteudo-' + aba).style.display = 'block';
        }
    </script>
</body>

</html>