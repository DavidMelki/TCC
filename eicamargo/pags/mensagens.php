<?php include '../php/sessao.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eicamargo - Mensagens</title>

    <!-- Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS Externo -->
    <link rel="stylesheet" href="../css/mensagens.css">
    <link rel="icon" href="../css/img/logoeicamargo.png">
</head>

<body>

    <div class="app-container">

        <!-- ==================== COLUNA ESQUERDA (MENU) ==================== -->
        <aside class="sidebar-left">
            <div class="logo">
                <div class="logo-icon"><img class="logo-icon" src="../css/img/logoeicamargo.png"></div>
                <div class="logo-text"></div>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="sugestoes.php" id="link">
                        <i class="bi bi-lightbulb-fill"></i> Sugestões
                    </a>
                </li>

                <li class="nav-item">
                    <a href="vendas.php" id="link">
                        <i class="fa-solid fa-cart-shopping"></i> Vendas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="comunicados.php" id="link">
                        <i class="fa-solid fa-bullhorn"></i> Comunicados
                    </a>
                </li>

                <li class="nav-item active">
                    <a href="mensagens.php" id="link">
                        <i class="fa-regular fa-envelope"></i> Mensagens
                    </a>
                </li>

                <li class="nav-item">
                    <a href="curso.php" id="link">
                        <i class="fa-solid fa-graduation-cap"></i> Curso
                    </a>
                </li>

                <li class="nav-item">
                    <a href="perfil.php" id="link">
                        <i class="fa-regular fa-user"></i> Perfil
                    </a>
                </li>
            </ul>

            <!-- Rodapé da Sidebar fixado na parte inferior -->
            <div class="profile-footer" style="margin-top: auto;">
                <div class="profile-info">
                    <div class="avatar" style="width: 40px; height: 40px; border-radius: 50%; background-image: url('../uploads/<?php echo !empty($usuarioLogado['foto_perfil']) ? htmlspecialchars($usuarioLogado['foto_perfil']) : 'default.png'; ?>'); background-size: cover; background-position: center;"></div>
                    <div class="dados">
                        <span class="nome" style="font-weight: bold; font-size: 14px; display: block;"><?php echo htmlspecialchars($usuarioLogado['nome']); ?></span>
                        <span class="usuario" style="font-size: 12px; color: #666; display: block;">@<?php echo strtolower(str_replace(' ', '', $usuarioLogado['nome'])); ?></span>
                    </div>
                </div>
                <i class="bi bi-three-dots"></i>
            </div>
        </aside>

        <!-- ================= TELA DE MENSAGENS ================= -->
        <div class="mensagens">
            <!-- ================= LISTA DE CONVERSAS ================= -->
            <section class="lista-conversas">
                <div class="topo-conversas">
                    <h2>Mensagens</h2>
                    <div class="pesquisa">
                        <i class="bi bi-search"></i>
                        <input type="text" id="inputPesquisaUsuario" placeholder="Pesquisar usuário para conversar">
                    </div>

                    <div class="filtros">
                        <button class="ativo" id="btnFiltroTodas">Todas</button>
                    </div>
                </div>

                <!-- Lista carregada via JS -->
                <div class="conversas" id="containerConversas">
                    <p style="text-align: center; color: #999; margin-top: 20px;">Carregando conversas...</p>
                </div>
            </section>

            <!-- ================= CHAT ================= -->
            <section class="chat" id="secaoChat" style="display: flex;">
                <div class="chat-topo" id="chatTopo" style="visibility: hidden;">
                    <div class="usuario-chat">
                        <div class="avatar-post" id="chatAvatar"></div>
                        <div>
                            <h3 id="chatNome">Selecione uma conversa</h3>
                            <span id="chatTag">@usuario</span>
                        </div>
                    </div>

                    <div class="icones-chat">
                        <i class="bi bi-telephone"></i>
                        <i class="bi bi-camera-video"></i>
                        <i class="bi bi-info-circle"></i>
                    </div>
                </div>

                <div class="chat-mensagens" id="containerMensagens">
                    <div style="text-align: center; color: #aaa; margin-top: 50px;">
                        Selecione um usuário para visualizar a conversa.
                    </div>
                </div>

                <!-- Campo para enviar mensagem -->
                <div class="enviar" id="boxEnviar" style="visibility: hidden;">
                    <i class="bi bi-emoji-smile"></i>
                    <i class="bi bi-paperclip"></i>
                    <input type="text" id="inputMensagem" placeholder="Digite uma mensagem...">
                    <i class="bi bi-image"></i>

                    <button class="botao-enviar" id="btnEnviar">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </section>
        </div>
    </div>

    <!-- Script principal de mensagens -->
    <script src="../js/jsmensagens/mensagens.js"></script>
</body>
</html>