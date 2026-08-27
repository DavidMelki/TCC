<?php include '../php/sessao.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camargo Aranha - Vendas</title>
    <!-- Ícones FontAwesome e Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Importando o CSS externo -->
    <link rel="icon" type="image/icon" href="../css/img/logoeicamargo.png">
    <link rel="stylesheet" href="../css/vendas.css">
</head>

<body>

    <div class="app-container">

        <!-- ==================== COLUNA ESQUERDA (MENU) ==================== -->
        <aside class="sidebar-left">
            <div class="logo">
                <div class="logo-icon"><img class="logo-icon" src="../css/img/logoeicamargo.png" alt="Logo"></div>
                <div class="logo-text"></div>
            </div>

            <ul class="nav-menu">
                <li>
                    <a href="sugestoes.php" class="nav-item">
                        <i class="bi bi-lightbulb-fill"></i> Sugestões
                    </a>
                </li>

                <li class="nav-item active">
                    <a href="vendas.php" id="link">
                        <i class="fa-solid fa-cart-shopping"></i> Vendas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="comunicados.php" id="link">
                        <i class="fa-solid fa-bullhorn"></i> Comunicados
                    </a>
                </li>

                <li class="nav-item">
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

            <button class="btn-publish btn-abrir-modal-venda"><i class="fa-solid fa-plus"></i> Publicar venda</button>

            <!-- Rodapé da Sidebar Dinâmico -->
            <div class="profile-footer">
                <div class="profile-info">
                    <div class="profile-avatar" style="background-image: url('../uploads/<?php echo !empty($usuarioLogado['foto_perfil']) ? htmlspecialchars($usuarioLogado['foto_perfil']) : 'default.png'; ?>'); background-size: cover; background-position: center;"></div>
                    <div>
                        <div class="profile-name"><?php echo htmlspecialchars($usuarioLogado['nome']); ?></div>
                        <div class="profile-username">@<?php echo strtolower(str_replace(' ', '', $usuarioLogado['nome'])); ?></div>
                    </div>
                </div>
                <i class="fa-solid fa-ellipsis menu-dots"></i>
            </div>
        </aside>

        <!-- ==================== COLUNA CENTRAL (FEED) ==================== -->
        <main class="feed-area">

            <div class="page-header">
                <h1>Vendas/Reservas</h1>
                <p>Compre de colegas, apoie a escola e fortaleça nossa comunidade.</p>
            </div>

            <!-- Lista de Produtos -->
            <div class="products-list">

                <!-- Produto 1 -->
                <div class="product-card">
                    <div class="product-user">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXqjeMrk4ndy8et9CierT1D6GvmQGwxY9iGELLZ3El2g&s=10"
                            alt="Avatar" class="user-avatar">
                        <div class="user-name">João V.</div>
                        <div class="user-time">2h atrás</div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-title">Cookies de Chocolate 🍪</h3>
                        <p class="product-desc">Cookies macios por dentro e crocantes por fora. Feitos com muito amor!</p>
                        <div class="product-tags">
                            <span class="tag">Cookies</span>
                        </div>
                    </div>

                    <div class="product-image-area">
                        <img src="https://docepedia.com/_next/image?url=https%3A%2F%2Fadmin.docepedia.com%2Fsite%2Fuploads%2F2021%2F07%2Fcookies-d-768x768-1.jpg&w=1080&q=75"
                            alt="Imagem do produto" class="product-image">
                    </div>

                    <div class="product-sidebar">
                        <div class="product-price-area">
                            <span class="product-price">R$ 12,00</span>
                            <span class="product-bloc">@ 1º andar</span>
                        </div>
                        <i class="fa-regular fa-bookmark save-icon"></i>
                        <div class="product-stats">
                            <span><i class="fa-regular fa-comment"></i> 2</span>
                            <span class="likes"><i class="fa-regular fa-heart"></i> 15</span>
                        </div>
                    </div>
                </div>

                <!-- Produto 2 -->
                <div class="product-card">
                    <div class="product-user">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXqjeMrk4ndy8et9CierT1D6GvmQGwxY9iGELLZ3El2g&s=10"
                            alt="Avatar" class="user-avatar">
                        <div class="user-name">Daniel R.</div>
                        <div class="user-time">4h atrás</div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-title">Bolo de Cenoura com Chocolate 🥕</h3>
                        <p class="product-desc">Fofinho e com cobertura de brigadeiro. Perfeito para o café da tarde!</p>
                        <div class="product-tags">
                            <span class="tag">Bolos</span>
                        </div>
                    </div>

                    <div class="product-image-area">
                        <img src="https://images.unsplash.com/photo-1621303837174-89787a7d4729?w=300&h=300&fit=crop"
                            alt="Imagem do produto" class="product-image">
                    </div>

                    <div class="product-sidebar">
                        <div class="product-price-area">
                            <span class="product-price">R$ 18,00</span>
                            <span class="product-bloc">@ 3º andar</span>
                        </div>
                        <i class="fa-regular fa-bookmark save-icon"></i>
                        <div class="product-stats">
                            <span><i class="fa-regular fa-comment"></i> 3</span>
                            <span class="likes"><i class="fa-regular fa-heart"></i> 21</span>
                        </div>
                    </div>
                </div>

                <!-- Produto 3 -->
                <div class="product-card">
                    <div class="product-user">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXqjeMrk4ndy8et9CierT1D6GvmQGwxY9iGELLZ3El2g&s=10"
                            alt="Avatar" class="user-avatar">
                        <div class="user-name">Davi F.</div>
                        <div class="user-time">1d atrás</div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-title">Torta de Limão 🍋</h3>
                        <p class="product-desc">Cremosa, leve e com o toque perfeito de limão. Base crocante que derrete na boca!</p>
                        <div class="product-tags">
                            <span class="tag">Tortas</span>
                        </div>
                    </div>

                    <div class="product-image-area">
                        <img src="https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=300&h=300&fit=crop"
                            alt="Imagem do produto" class="product-image">
                    </div>

                    <div class="product-sidebar">
                        <div class="product-price-area">
                            <span class="product-price">R$ 15,00</span>
                            <span class="product-bloc">@ 2º andar</span>
                        </div>
                        <i class="fa-regular fa-bookmark save-icon"></i>
                        <div class="product-stats">
                            <span><i class="fa-regular fa-comment"></i> 1</span>
                            <span class="likes"><i class="fa-regular fa-heart"></i> 12</span>
                        </div>
                    </div>
                </div>

                <!-- Produto 4 -->
                <div class="product-card">
                    <div class="product-user">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXqjeMrk4ndy8et9CierT1D6GvmQGwxY9iGELLZ3El2g&s=10"
                            alt="Avatar" class="user-avatar">
                        <div class="user-name">Davi T.</div>
                        <div class="user-time">2d atrás</div>
                    </div>

                    <div class="product-info">
                        <h3 class="product-title">Brownie com Nozes 🍫</h3>
                        <p class="product-desc">Fudgy, chocolatudo e com nozes crocantes. Ideal para matar a vontade!</p>
                        <div class="product-tags">
                            <span class="tag">Doces</span>
                        </div>
                    </div>

                    <div class="product-image-area">
                        <img src="https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=300&h=300&fit=crop"
                            alt="Imagem do produto" class="product-image">
                    </div>

                    <div class="product-sidebar">
                        <div class="product-price-area">
                            <span class="product-price">R$ 10,00</span>
                            <span class="product-bloc">@ 3º andar</span>
                        </div>
                        <i class="fa-regular fa-bookmark save-icon"></i>
                        <div class="product-stats">
                            <span><i class="fa-regular fa-comment"></i> 2</span>
                            <span class="likes"><i class="fa-regular fa-heart"></i> 9</span>
                        </div>
                    </div>
                </div>

            </div>

        </main>

        <!-- ==================== COLUNA DIREITA (SIDEBAR) ==================== -->
        <aside class="sidebar-right">

            <!-- Comunidade -->
            <div class="side-card community">
                <div class="card-title">
                    <div class="title-icon"><i class="fa-regular fa-heart"></i></div>
                    Nossa comunidade
                </div>
                <div class="community-text">
                    <p>Aqui cada venda ajuda a fortalecer nossa escola e cria oportunidades para todos.</p>
                    <p>Compre, compartilhe e faça parte!</p>
                </div>
            </div>

            <!-- Dicas para vender mais -->
            <div class="side-card">
                <div class="card-title">Dicas para vender mais</div>

                <div class="dica-item">
                    <div class="dica-icon"><i class="fa-solid fa-camera"></i></div>
                    <div class="dica-text">
                        <h4>Use fotos bonitas</h4>
                        <p>Fotos bem iluminadas e nítidas chamam mais atenção.</p>
                    </div>
                </div>

                <div class="dica-item">
                    <div class="dica-icon"><i class="fa-solid fa-pen"></i></div>
                    <div class="dica-text">
                        <h4>Capriche na descrição</h4>
                        <p>Conte os ingredientes, tamanho, sabor e validade.</p>
                    </div>
                </div>

                <div class="dica-item">
                    <div class="dica-icon"><i class="fa-regular fa-clock"></i></div>
                    <div class="dica-text">
                        <h4>Seja rápido nas respostas</h4>
                        <p>Responder rápido aumenta a chance de vender mais.</p>
                    </div>
                </div>

            </div>

            <!-- Ranking -->
            <div class="side-card">
              <div class="card-title">Mais populares desta semana</div> 
              <div class="popular-list">

            </div>

        </aside>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.querySelectorAll('.btn-abrir-modal-venda').forEach(button => {
            button.addEventListener('click', function () {
                Swal.fire({
                    title: '<strong>Publicar Venda</strong>',
                    html: `
                        <form id="formPublicarVenda" style="text-align: left; display: flex; flex-direction: column; gap: 14px;">
                            
                            <!-- Área de Upload de Foto/Vídeo -->
                            <div>
                                <label for="swal-midia" class="upload-area-box" id="uploadBox">
                                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 28px; color: #e63c3c; margin-bottom: 6px;"></i>
                                    <span id="uploadText" style="font-size: 13px; color: #8c8c8c; text-align: center;">Clique para enviar uma foto ou vídeo do produto</span>
                                    <input type="file" id="swal-midia" accept="image/*,video/*" style="display: none;" onchange="previewMidia(this)">
                                </label>
                                <div id="mediaPreviewContainer" style="display: none; margin-top: 10px; position: relative;">
                                    <div id="mediaPreview"></div>
                                    <button type="button" onclick="removerMidia()" style="position: absolute; top: 6px; right: 6px; background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer;">&times;</button>
                                </div>
                            </div>

                            <!-- Título do Produto -->
                            <div>
                                <input type="text" id="swal-titulo" class="swal2-input custom-swal-input" placeholder="Título do produto (ex: Cookie de Chocolate)" style="margin: 0; width: 100%;">
                            </div>

                            <!-- Preço e Local -->
                            <div style="display: flex; gap: 10px;">
                                <input type="text" id="swal-preco" class="swal2-input custom-swal-input" placeholder="Preço (ex: R$ 12,00)" style="margin: 0; width: 50%;">
                                <input type="text" id="swal-local" class="swal2-input custom-swal-input" placeholder="Local (ex: 1º andar)" style="margin: 0; width: 50%;">
                            </div>

                            <!-- Legenda / Descrição -->
                            <div>
                                <textarea id="swal-legenda" class="swal2-textarea custom-swal-input" placeholder="Escreva uma legenda ou detalhes do produto..." style="width: 100%; margin: 0; height: 90px; resize: none;"></textarea>
                            </div>

                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Publicar Venda',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#e63c3c',
                    cancelButtonColor: '#8c8c8c',
                    focusConfirm: false,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const midiaInput = document.getElementById('swal-midia');
                        const titulo = document.getElementById('swal-titulo').value.trim();
                        const preco = document.getElementById('swal-preco').value.trim();
                        const local = document.getElementById('swal-local').value.trim();
                        const legenda = document.getElementById('swal-legenda').value.trim();

                        if (!titulo) {
                            Swal.showValidationMessage('Por favor, informe o título do produto!');
                            return false;
                        }

                        if (!preco) {
                            Swal.showValidationMessage('Por favor, informe o preço!');
                            return false;
                        }

                        if (!legenda) {
                            Swal.showValidationMessage('Por favor, escreva uma legenda/descrição!');
                            return false;
                        }

                        const formData = new FormData();
                        if (midiaInput.files.length > 0) {
                            formData.append('midia', midiaInput.files[0]);
                        }
                        formData.append('titulo', titulo);
                        formData.append('preco', preco);
                        formData.append('local', local);
                        formData.append('legenda', legenda);

                        return fetch('vendas.php', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText);
                                }
                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Erro ao enviar: ${error}`);
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed && result.value && result.value.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Publicado!',
                            text: result.value.message,
                            confirmButtonColor: '#02d569'
                        });
                    }
                });
            });
        });

        // Função de Preview da Mídia selecionada
        function previewMidia(input) {
            const file = input.files[0];
            const previewContainer = document.getElementById('mediaPreviewContainer');
            const preview = document.getElementById('mediaPreview');
            const uploadBox = document.getElementById('uploadBox');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (file.type.startsWith('image/')) {
                        preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: 8px;">`;
                    } else if (file.type.startsWith('video/')) {
                        preview.innerHTML = `<video src="${e.target.result}" controls style="width: 100%; max-height: 180px; border-radius: 8px;"></video>`;
                    }
                    previewContainer.style.display = 'block';
                    uploadBox.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        // Função para remover a mídia pré-visualizada
        function removerMidia() {
            document.getElementById('swal-midia').value = '';
            document.getElementById('mediaPreview').innerHTML = '';
            document.getElementById('mediaPreviewContainer').style.display = 'none';
            document.getElementById('uploadBox').style.display = 'flex';
        }

        <!-- Script do SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Script do Vendas -->
    <script src="../js/jsvendas/vendas.js?v=<?php echo time(); ?>"></script>

    </script>
</body>

</html>