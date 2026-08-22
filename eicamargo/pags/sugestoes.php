<?php include '../php/sessao.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eicamargo - Sugestões</title>

    <!-- Ícones de bibliotecas externas -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Library SweetAlert2 (JS) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Library Emoji Mart (Seletor de Emojis) -->
    <script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>

    <!-- Folhas de Estilo CSS -->
    <link rel="stylesheet" href="../css/sugestoes.css">
    <link rel="stylesheet" href="../css/animacoes-sugestoes.css">

    <link rel="icon" type="image/icon" href="../css/img/logoeicamargo.png">
</head>

<body>

    <div class="app-container">

        <!-- =========================================================
             MENU LATERAL DA ESQUERDA (SIDEBAR)
             ========================================================= -->
        <aside class="sidebar-left">

            <div class="logo">
                <img src="../css/img/logoeicamargo.png" alt="Logo">
            </div>

            <ul class="nav-menu">

                <li>
                    <a href="sugestoes.php" class="nav-item active">
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
                    <a href="perfil.php" class="nav-item">
                        <i class="fa-regular fa-user"></i> Perfil
                    </a>
                </li>

            </ul>

            <button class="btn-nova-sugestao" id="btnNovaSugestao">
                <i class="bi bi-plus-lg"></i> Nova sugestão
            </button>

            <!-- Rodapé da Sidebar Dinâmico -->
            <div class="profile-footer">
                <div class="profile-info">
                    <div class="avatar" style="background-image: url('../uploads/<?php echo !empty($usuarioLogado['foto_perfil']) ? htmlspecialchars($usuarioLogado['foto_perfil']) : 'default.png'; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="dados">
                        <span class="nome"><?php echo htmlspecialchars($usuarioLogado['nome']); ?></span>
                        <span class="usuario">@<?php echo strtolower(str_replace(' ', '', $usuarioLogado['nome'])); ?></span>
                    </div>
                </div>
                <i class="bi bi-three-dots"></i>
            </div>

        </aside>


        <!-- =========================================================
             ÁREA CENTRAL DE SUGESTÕES (FEED)
             ========================================================= -->
        <main class="detalhe-sugestao">

            <div class="detalhe-header">
                <h1 style="text-align: center;">Sugestões</h1>
            </div>

            <div class="detalhe-body">
                <!-- Posts de Sugestão serão inseridos aqui via JavaScript -->
            </div>

        </main>


        <!-- =========================================================
             PAINEL LATERAL DIREITO (FAVORITAS - APENAS COM LIKES)
             ========================================================= -->
        <section class="painel-sugestoes">

            <div class="top-sugestoes">
                <h2>Sugestões Favoritas</h2>
            </div>

            <div class="lista-items">
                <!-- Preenchido dinamicamente via JS apenas com sugestões que receberam curtidas -->
            </div>

        </section>

    </div>


    <!-- =========================================================
         SCRIPTS E LÓGICA JAVASCRIPT
         ========================================================= -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="../js/jssugestoes/sugestoes.js?v=<?php echo time(); ?>"></script>

</body>

</html>