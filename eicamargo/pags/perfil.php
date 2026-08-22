<?php
session_start();
// Se o usuário não estiver logado, redireciona para a tela de login
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

    // Busca os dados atualizados do usuário no banco
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // Se por acaso o usuário não existir, destrói a sessão e manda pro login
        session_destroy();
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eicamargo - Perfil</title>
    <!-- Ícones Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="icon" type="image/icon" href="../css/img/logoeicamargo.png">
    <meta name="author" content="Alexandre Castello, David Melquiades, Bruna de Mello e Julia Akemi">
</head>

<body>

    <div class="app-container">

        <!-- ==================== COLUNA 1: MENU LATERAL ==================== -->
        <aside class="sidebar-left">
            <div class="logo">
                <img src="../css/img/logoeicamargo.png" alt="Logo">
            </div>

            <ul class="nav-menu">
                <li>
                    <a href="sugestoes.php" class="nav-item">
                        <i class="bi bi-lightbulb-fill"></i> Sugestões
                    </a>
                </li>
                <li>
                    <a href="vendas.php" class="nav-item">
                        <i class="fa-solid fa-cart-shopping"></i> Vendas
                    </a>
                </li>
                <li>
                    <a href="comunicados.php" class="nav-item">
                        <i class="fa-solid fa-bullhorn"></i> Comunicados
                    </a>
                </li>
                <li>
                    <a href="mensagens.php" class="nav-item">
                        <i class="fa-regular fa-envelope"></i> Mensagens
                    </a>
                </li>
                <li>
                    <a href="curso.php" class="nav-item">
                        <i class="fa-solid fa-graduation-cap"></i> Curso
                    </a>
                </li>
                <li>
                    <a href="perfil.php" class="nav-item active">
                        <i class="fa-regular fa-user"></i> Perfil
                    </a>
                </li>
            </ul>

            <div class="profile-footer">
                <div class="profile-info">
                    <div class="avatar" style="background-image: url('../uploads/<?php echo !empty($usuario['foto_perfil']) ? htmlspecialchars($usuario['foto_perfil']) : 'default.png'; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="dados">
                        <span class="nome"><?php echo htmlspecialchars($usuario['nome']); ?></span>
                        <span class="usuario">@<?php echo strtolower(str_replace(' ', '', $usuario['nome'])); ?></span>
                    </div>
                </div>
                <i class="bi bi-three-dots"></i>
            </div>
        </aside>

        <!-- ==================== COLUNA 2: FEED CENTRAL (PERFIL) ==================== -->
        <main class="feed">

            <!-- Capa e Foto de Perfil -->
            <div class="capa"></div>

            <div class="perfil-header">
                <div class="foto-perfil" style="background-image: url('../uploads/<?php echo !empty($usuario['foto_perfil']) ? htmlspecialchars($usuario['foto_perfil']) : 'default.png'; ?>'); background-size: cover; background-position: center;"></div>
                <button class="btn-editar">Editar Perfil</button>
            </div>

            <!-- Informações do Usuário -->
            <div class="info-perfil">
                <h2><?php echo htmlspecialchars($usuario['nome']); ?></h2>
                <span class="usuario">@<?php echo strtolower(str_replace(' ', '', $usuario['nome'])); ?></span>
                <p class="descricao">
                    <?php echo htmlspecialchars($usuario['biografia'] ?? 'Nenhuma biografia informada.'); ?>
                </p>
            </div>

            <!-- Abas do Perfil -->
            <div class="abas">
                <div class="aba ativa">Sugestões</div>
                <div class="aba"></div> 
            </div>

            <!-- Conteúdo das Abas (Feed do usuário) -->
            <div class="lista-posts">
                <div class="post">
                    <div class="avatar-post" style="background-image: url('../uploads/<?php echo !empty($usuario['foto_perfil']) ? htmlspecialchars($usuario['foto_perfil']) : 'default.png'; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="conteudo-post">
                        <div class="post-header">
                            <h4><?php echo htmlspecialchars($usuario['nome']); ?></h4>
                            <span>@<?php echo strtolower(str_replace(' ', '', $usuario['nome'])); ?> · 2d</span>
                        </div>
                        <p>Minha primeira sugestão enviada na plataforma!</p>
                        <div class="post-acoes">
                            <button><i class="bi bi-heart"></i> 12</button>
                            <button><i class="bi bi-chat"></i> 2</button>
                            <button><i class="bi bi-share"></i></button>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <!-- ==================== COLUNA 3: DIREITA ==================== -->
        <aside class="right-bar">

            <div class="card-info">
                <h3>Seu Perfil</h3>
                <p>Mantenha Suas informações atualizadas para que outros alunos e professores reconheçam suas publicações.</p>
            </div>

            <div class="links">
                <a href="#">Termos de Serviço</a>
                <a href="#">Política de Privacidade</a>
                <a href="#">Política de Cookies</a>
                <a href="#">Acessibilidade</a>
                <a href="#">Informações de Anúncios</a>
                <a href="#">Mais</a>
            </div>

            <p class="copyright">© 2026 Camargo aranha.</p>

        </aside>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/jsperfil/alterarperfil.js"></script>
</body>

</html>