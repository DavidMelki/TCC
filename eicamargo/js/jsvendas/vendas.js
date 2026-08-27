let vendasCache = [];

// Helper functions para gerenciar localStorage
function obterFavoritosSalvos() {
    const salvos = localStorage.getItem('vendas_favoritadas');
    return salvos ? JSON.parse(salvos) : [];
}

function salvarFavoritosNoStorage(favoritosIds) {
    localStorage.setItem('vendas_favoritadas', JSON.stringify(favoritosIds));
}

document.addEventListener('DOMContentLoaded', () => {
    carregarVendas();

    document.querySelectorAll('.btn-abrir-modal-venda').forEach(button => {
        button.addEventListener('click', abrirModalVenda);
    });

    const productsList = document.querySelector('.products-list');
    if (productsList) {
        productsList.addEventListener('click', (e) => {
            const favBtn = e.target.closest('.favorite-btn');
            const card = e.target.closest('.product-card');

            if (card) {
                const id = card.getAttribute('data-id');
                const venda = vendasCache.find(v => v.id == id);

                // Clique no botão de favoritar
                if (favBtn && venda) {
                    toggleFavorito(venda, favBtn);
                    return;
                }

                // Clique no card para abrir detalhes
                if (venda) {
                    abrirModalDetalhes(venda);
                }
            }
        });
    }
});

async function carregarVendas() {
    const productsList = document.querySelector('.products-list');
    if (!productsList) return;

    try {
        const res = await fetch('../php/phpvendas/Bvendas.php');
        const data = await res.json();

        if (data.status === 'success' && data.vendas && data.vendas.length > 0) {
            const favoritosSalvos = obterFavoritosSalvos();

            vendasCache = data.vendas.map(v => {
                const jaFavoritado = favoritosSalvos.includes(String(v.id));
                let likesBase = parseInt(v.likes || v.favoritos || 0);

                return {
                    ...v,
                    likes: jaFavoritado ? Math.max(likesBase, 1) : likesBase,
                    favoritado: jaFavoritado
                };
            });

            renderizarFeed();
        } else {
            vendasCache = [];
            productsList.innerHTML = `<p class="empty-message">Nenhuma venda publicada ainda.</p>`;
            renderizarTopPopulares();
        }
    } catch (e) {
        console.error('Erro ao carregar vendas:', e);
    }
}

function renderizarFeed() {
    const productsList = document.querySelector('.products-list');
    if (productsList) {
        productsList.innerHTML = vendasCache.map(venda => criarHTMLProduto(venda)).join('');
    }
    renderizarTopPopulares();
}

function criarHTMLProduto(venda) {
    let midiaHTML = '';
    
    if (venda.midia && venda.midia.trim() !== '') {
        const isVideo = venda.midia.endsWith('.mp4') || venda.midia.endsWith('.mov');
        midiaHTML = isVideo 
            ? `<video src="${venda.midia}" class="product-image"></video>` 
            : `<img src="${venda.midia}" alt="${venda.titulo}" class="product-image" onerror="this.src='https://via.placeholder.com/300x300?text=Sem+Imagem';">`;
    } else {
        midiaHTML = `<img src="https://via.placeholder.com/300x300?text=Sem+Imagem" alt="${venda.titulo}" class="product-image">`;
    }

    const localizacao = venda.local ? `${venda.local}` : 'Andar não informado';
    const isFavorito = venda.favoritado ? 'active' : '';
    const iconClass = venda.favoritado ? 'fa-solid fa-heart' : 'fa-regular fa-heart';

    return `
        <div class="product-card" data-id="${venda.id}">
            <div class="product-image-area">
                ${midiaHTML}
                <div class="favorite-btn ${isFavorito}">
                    <i class="${iconClass}"></i>
                </div>
            </div>

            <div class="product-info">
                <h3 class="product-title">${venda.titulo}</h3>
                <div class="product-price">${venda.preco}</div>
                <div class="product-meta">
                    <i class="fa-solid fa-location-dot"></i> ${localizacao} • ${venda.nome || 'Vendedor'}
                </div>
            </div>
        </div>
    `;
}

function toggleFavorito(venda, elementBtn) {
    venda.favoritado = !venda.favoritado;
    venda.likes += venda.favoritado ? 1 : -1;

    // Atualiza storage
    let favoritosSalvos = obterFavoritosSalvos().map(String);
    const vendaIdStr = String(venda.id);

    if (venda.favoritado) {
        if (!favoritosSalvos.includes(vendaIdStr)) {
            favoritosSalvos.push(vendaIdStr);
        }
    } else {
        favoritosSalvos = favoritosSalvos.filter(id => id !== vendaIdStr);
    }
    salvarFavoritosNoStorage(favoritosSalvos);

    // Atualiza o estado e cor do ícone no card
    const icon = elementBtn.querySelector('i');
    if (venda.favoritado) {
        elementBtn.classList.add('active');
        icon.className = 'fa-solid fa-heart';
    } else {
        elementBtn.classList.remove('active');
        icon.className = 'fa-regular fa-heart';
    }

    renderizarTopPopulares();
}

function renderizarTopPopulares() {
    const popularContainer = document.querySelector('.popular-list');
    if (!popularContainer) return;

    // Filtra apenas itens com pelo menos 1 curtida
    const popularesComCurtidas = vendasCache.filter(venda => venda.likes > 0);

    if (popularesComCurtidas.length === 0) {
        popularContainer.innerHTML = '<span style="font-size: 13px; color: #8c8c8c; text-align: center; display: block; padding: 10px 0;">Nenhum produto favoritado ainda.</span>';
        return;
    }

    const top3 = popularesComCurtidas
        .sort((a, b) => b.likes - a.likes)
        .slice(0, 3);

    popularContainer.innerHTML = top3.map((venda, index) => {
        const foto = (venda.midia && !venda.midia.endsWith('.mp4')) ? venda.midia : 'https://via.placeholder.com/100?text=Sem+Foto';
        return `
            <div class="ranking-item" style="cursor: pointer;" onclick="abrirModalDetalhesById(${venda.id})">
                <div class="rank-num">${index + 1}</div>
                <img src="${foto}" class="rank-img">
                <div class="rank-info">
                    <strong>${venda.titulo}</strong>
                    <span>por ${venda.nome || 'Aluno'}</span>
                </div>
                <div class="rank-hearts ${venda.favoritado ? 'active' : ''}">
                    <i class="${venda.favoritado ? 'fa-solid' : 'fa-regular'} fa-heart" style="${venda.favoritado ? 'color: #e63c3c;' : ''}"></i>
                    <span>${venda.likes}</span>
                </div>
            </div>
        `;
    }).join('');
}

function abrirModalDetalhesById(id) {
    const venda = vendasCache.find(v => v.id == id);
    if (venda) abrirModalDetalhes(venda);
}

function abrirModalVenda() {
    Swal.fire({
        title: '<strong>Publicar Venda</strong>',
        html: `
            <form id="formPublicarVenda" style="text-align: left; display: flex; flex-direction: column; gap: 14px;">
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

                <div>
                    <input type="text" id="swal-titulo" class="swal2-input custom-swal-input" placeholder="Título do produto (ex: Cookie de Chocolate)" style="margin: 0; width: 100%;">
                </div>

                <div style="display: flex; gap: 10px;">
                    <input type="text" id="swal-preco" class="swal2-input custom-swal-input" placeholder="Preço (ex: R$ 12,00)" style="margin: 0; width: 50%;">
                    <input type="text" id="swal-local" class="swal2-input custom-swal-input" placeholder="Local (ex: 1º andar)" style="margin: 0; width: 50%;">
                </div>

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

            if (!titulo || !preco || !legenda) {
                Swal.showValidationMessage('Preencha os campos de título, preço e descrição!');
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

            return fetch('../php/phpvendas/Bvendas.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .catch(error => {
                Swal.showValidationMessage(`Erro ao enviar: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value && result.value.status === 'success') {
            carregarVendas();
            Swal.fire({
                icon: 'success',
                title: 'Publicado!',
                text: result.value.message,
                confirmButtonColor: '#02d569'
            });
        }
    });
}

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

function removerMidia() {
    document.getElementById('swal-midia').value = '';
    document.getElementById('mediaPreview').innerHTML = '';
    document.getElementById('mediaPreviewContainer').style.display = 'none';
    document.getElementById('uploadBox').style.display = 'flex';
}

function abrirModalDetalhes(venda) {
    const nomeSeguro = encodeURIComponent(venda.nome || 'Usuário');
    let fotoPerfil = `https://ui-avatars.com/api/?name=${nomeSeguro}&background=e63946&color=fff&size=128`;
    if (venda.foto_perfil && venda.foto_perfil.trim() !== '') {
        fotoPerfil = venda.foto_perfil;
    }

    let midiaHTML = '';
    if (venda.midia && venda.midia.trim() !== '') {
        const isVideo = venda.midia.endsWith('.mp4') || venda.midia.endsWith('.mov');
        midiaHTML = isVideo
            ? `<video src="${venda.midia}" controls style="width: 100%; max-height: 250px; border-radius: 8px; margin-bottom: 12px;"></video>`
            : `<img src="${venda.midia}" style="width: 100%; max-height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 12px;">`;
    }

    Swal.fire({
        title: '',
        html: `
            <div style="text-align: left; font-family: var(--font-main);">
                ${midiaHTML}
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <img src="${fotoPerfil}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <strong style="display: block; font-size: 14px; color: #1a1a1a;">${venda.nome || 'Aluno(a)'}</strong>
                        <span style="font-size: 12px; color: #8c8c8c;"><i class="fa-solid fa-location-dot"></i> ${venda.local || 'Andar não informado'}</span>
                    </div>
                </div>

                <h2 style="font-size: 18px; font-weight: 700; color: #1a1a1a; margin-bottom: 6px;">${venda.titulo}</h2>
                <div style="font-size: 20px; font-weight: 800; color: #e63c3c; margin-bottom: 14px;">${venda.preco}</div>

                <div style="background-color: #f7f8fa; padding: 12px; border-radius: 8px; font-size: 14px; color: #4a4a4a; line-height: 1.5; white-space: pre-wrap;">${venda.legenda || 'Sem descrição.'}</div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'Fechar',
        confirmButtonColor: '#e63c3c'
    });
}