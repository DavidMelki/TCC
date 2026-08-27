document.addEventListener('DOMContentLoaded', () => {
    carregarSugestoes();

    const btnTarget = document.getElementById('btnNovaSugestao') || document.querySelector('.btn-nova-sugestao') || Array.from(document.querySelectorAll('button, a')).find(el => el.textContent.includes('Nova sugestão'));

    if (btnTarget) {
        btnTarget.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalSugestao();
        });
    }

    // Delegação global de cliques
    document.addEventListener('click', function (e) {
        // Excluir Post
        const btnExcluir = e.target.closest('.btn-excluir-post');
        if (btnExcluir) {
            e.preventDefault();
            const cardPost = btnExcluir.closest('.card-sugestao-post');
            const sugestaoId = cardPost?.getAttribute('data-id');

            Swal.fire({
                title: 'Tem certeza?',
                text: "Esta sugestão será excluída permanentemente!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('sugestao_id', sugestaoId);

                    fetch('../php/phpsugestoes/excluir.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (cardPost) cardPost.remove();
                            atualizarFavoritas();

                            Swal.fire({
                                title: 'Excluído!',
                                text: 'A sugestão foi removida.',
                                icon: 'success',
                                confirmButtonColor: '#e63946'
                            });
                        })
                        .catch(err => console.error('Erro ao excluir:', err));
                }
            });
            return;
        }

        // Excluir Comentário
        const btnExcluirComent = e.target.closest('.btn-excluir-comentario');
        if (btnExcluirComent) {
            e.preventDefault();
            const itemComentario = btnExcluirComent.closest('.item-comentario');
            const comentarioId = itemComentario?.getAttribute('data-comentario-id');

            Swal.fire({
                title: 'Excluir comentário?',
                text: "Esta ação não poderá ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63946',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('comentario_id', comentarioId);

                    fetch('../php/phpsugestoes/excluir_comentario.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                if (itemComentario) itemComentario.remove();
                            } else {
                                Swal.fire('Erro', data.message || 'Não foi possível excluir', 'error');
                            }
                        })
                        .catch(err => console.error('Erro ao excluir comentário:', err));
                }
            });
            return;
        }

        // Curtir / Descurtir
        const btnLike = e.target.closest('.btn-like');
        if (btnLike) {
            e.preventDefault();
            const cardPost = btnLike.closest('.card-sugestao-post');
            const sugestaoId = cardPost?.getAttribute('data-id');
            const countSpan = btnLike.querySelector('.like-count');
            let count = parseInt(countSpan?.textContent) || 0;
            const icon = btnLike.querySelector('i');

            let acao = 'curtir';
            if (btnLike.classList.contains('liked')) {
                btnLike.classList.remove('liked');
                count--;
                acao = 'descurtir';
                if (icon) {
                    icon.classList.remove('bi-hand-thumbs-up-fill');
                    icon.classList.add('bi-hand-thumbs-up');
                }
            } else {
                btnLike.classList.add('liked');
                count++;
                if (icon) {
                    icon.classList.remove('bi-hand-thumbs-up');
                    icon.classList.add('bi-hand-thumbs-up-fill');
                }
            }

            if (countSpan) countSpan.textContent = count;
            atualizarFavoritas();

            const formData = new FormData();
            formData.append('sugestao_id', sugestaoId);
            formData.append('acao', acao);

            fetch('../php/phpsugestoes/curtir.php', {
                method: 'POST',
                body: formData
            }).catch(err => console.error('Erro ao registrar like:', err));
            return;
        }

        // Abrir/Fechar Comentários
        const btnComent = e.target.closest('.btn-coment');
        if (btnComent) {
            e.preventDefault();
            const cardPost = btnComent.closest('.card-sugestao-post');
            const secaoComentarios = cardPost?.querySelector('.secao-comentarios');

            if (secaoComentarios) {
                secaoComentarios.classList.toggle('ativo');
                btnComent.classList.toggle('comentario-ativo');
            }
            return;
        }

        // Seletor de Emojis
        const btnEmoji = e.target.closest('.btn-emoji');
        if (btnEmoji) {
            e.preventDefault();
            e.stopPropagation();

            const containerBox = btnEmoji.closest('.campo-comentario-box');
            const input = containerBox.querySelector('.input-comentario');
            let pickerContainer = containerBox.querySelector('.emoji-picker-container');

            if (pickerContainer) {
                pickerContainer.remove();
                return;
            }

            document.querySelectorAll('.emoji-picker-container').forEach(el => el.remove());

            pickerContainer = document.createElement('div');
            pickerContainer.className = 'emoji-picker-container';

            const picker = new EmojiMart.Picker({
                locale: 'pt',
                theme: 'light',
                onEmojiSelect: (emoji) => {
                    const start = input.selectionStart;
                    const end = input.selectionEnd;
                    const text = input.value;

                    input.value = text.substring(0, start) + emoji.native + text.substring(end);
                    input.selectionStart = input.selectionEnd = start + emoji.native.length;
                    input.focus();

                    pickerContainer.remove();
                }
            });

            pickerContainer.appendChild(picker);
            containerBox.style.position = 'relative';
            containerBox.appendChild(pickerContainer);
            return;
        } else if (!e.target.closest('.emoji-picker-container')) {
            document.querySelectorAll('.emoji-picker-container').forEach(el => el.remove());
        }

        // Enviar Comentário por Clique no Botão
        const btnEnviar = e.target.closest('.btn-enviar-comentario');
        if (btnEnviar) {
            e.preventDefault();
            enviarComentario(btnEnviar.closest('.card-sugestao-post'));
            return;
        }
    });

    // Enviar Comentário ao apertar "Enter" no input
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            const input = e.target.closest('.input-comentario');
            if (input) {
                e.preventDefault();
                enviarComentario(input.closest('.card-sugestao-post'));
            }
        }
    });
});

// Função centralizada para enviar o comentário
function enviarComentario(cardPost) {
    if (!cardPost) return;

    const input = cardPost.querySelector('.input-comentario');
    const comentarioTexto = input?.value.trim();
    const sugestaoId = cardPost.getAttribute('data-id');

    if (!comentarioTexto) {
        Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Escreva algo no comentário antes de enviar!' });
        return;
    }

    const formData = new FormData();
    formData.append('sugestao_id', sugestaoId);
    formData.append('comentario', comentarioTexto);

    fetch('../php/phpsugestoes/comentario.php', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const novoComentarioHTML = `
                <div class="item-comentario" data-comentario-id="${data.id}" style="background: #f8f9fa; padding: 10px 14px; border-radius: 12px; margin-bottom: 10px; font-size: 13px; color: #333; border: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong style="color: #e63946;">Você:</strong> ${comentarioTexto}
                    </div>
                    <button class="btn-excluir-comentario" title="Excluir comentário" style="border: none; background: transparent; color: #aaa; cursor: pointer; font-size: 14px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

                const listaComentarios = cardPost.querySelector('.lista-comentarios');
                if (listaComentarios) {
                    listaComentarios.insertAdjacentHTML('beforeend', novoComentarioHTML);
                }

                const secaoComentarios = cardPost.querySelector('.secao-comentarios');
                const btnCom = cardPost.querySelector('.btn-coment');
                if (secaoComentarios) secaoComentarios.classList.add('ativo');
                if (btnCom) btnCom.classList.add('comentario-ativo');

                if (input) input.value = '';
            } else {
                Swal.fire({ icon: 'error', title: 'Erro', text: data.message || 'Erro ao enviar' });
            }
        })
        .catch(err => console.error('Erro na requisição do comentário:', err));
}

async function carregarSugestoes() {
    const detalheBody = document.querySelector('.detalhe-body');
    if (!detalheBody) return;

    try {
        const res = await fetch('../php/phpsugestoes/listar.php');
        const data = await res.json();

        if (data.status === 'success' && data.sugestoes && data.sugestoes.length > 0) {
            detalheBody.innerHTML = data.sugestoes.map(post => criarHTMLPost(post)).join('');
        } else {
            detalheBody.innerHTML = `<p style="text-align: center; color: #999;">Nenhuma sugestão enviada ainda.</p>`;
        }
    } catch (e) {
        console.error('Erro ao buscar sugestões do servidor:', e);
        detalheBody.innerHTML = `<p style="text-align: center; color: #e63946;">Erro ao carregar sugestões.</p>`;
    }

    atualizarFavoritas();
}

function criarHTMLPost(post) {
    const curtidoClasse = post.liked ? 'liked' : '';
    const iconeLike = post.liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up';

    let comentariosHTML = '';
    const temComentarios = post.comentarios && Array.isArray(post.comentarios) && post.comentarios.length > 0;

    if (temComentarios) {
        comentariosHTML = post.comentarios.map(c => `
            <div class="item-comentario" data-comentario-id="${c.id}" style="background: #f8f9fa; padding: 10px 14px; border-radius: 12px; margin-bottom: 10px; font-size: 13px; color: #333; border: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #e63946;">${c.nome || 'Você'}:</strong> ${c.comentario}
                </div>
                <button class="btn-excluir-comentario" title="Excluir comentário" style="border: none; background: transparent; color: #aaa; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `).join('');
    }

    const nomeSeguro = encodeURIComponent(post.nome || 'Usuário');
    let fotoPerfil = `https://ui-avatars.com/api/?name=${nomeSeguro}&background=e63946&color=fff&size=128`;

    if (post.foto_perfil && post.foto_perfil.trim() !== '') {
        fotoPerfil = post.foto_perfil;
    }

    // Se já existem comentários salvos, mantém a seção aberta (classe "ativo" ou display block)
    const classeAtivo = temComentarios ? 'ativo' : '';
    const displaySecao = temComentarios ? 'block' : 'none';

    return `
        <div class="card-sugestao-post" data-id="${post.id}" style="margin-bottom: 30px; border: 1px solid #eee; padding: 20px; border-radius: 12px; background: #fff;">
            <div class="detalhe-header" style="padding-left:0; padding-right:0; display: flex; justify-content: space-between; align-items: flex-start; border-bottom: none;">
                <div class="autor-box" style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                    <img src="${fotoPerfil}" alt="Foto de perfil" style="width: 45px !important; height: 45px !important; min-width: 45px !important; min-height: 45px !important; border-radius: 50% !important; object-fit: cover !important; display: block !important; border: 1px solid #ddd;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=${nomeSeguro}&background=e63946&color=fff';">
                    
                    <div class="autor-dados" style="display: flex; flex-direction: column; align-items: flex-start; justify-content: center;">
                       <div style="display: flex; align-items: center; gap: 8px;">
                         <h3 style="margin: 0; font-size: 15px; font-weight: 600;">${post.nome || 'Você'}</h3>
                         <span class="autor-tag" style="color: #777; font-size: 13px; line-height: 1;">${post.usuario || '@usuario'}</span>
                       </div>

                        <div class="meta-info" style="color: #aaa; font-size: 12px; margin-top: 3px;">
                          ${post.tempo || 'Recente'}
                        </div>
                    </div>
                </div>
                <button class="btn-excluir-post" title="Excluir sugestão" style="border: none; background: transparent; color: #aaa; cursor: pointer; font-size: 16px;">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="secao-descricao" style="margin-bottom: 15px;">
                <p style="color: #333; line-height: 1.5; font-size: 15px;">${post.descricao}</p>
            </div>
            <div class="curtidas-bar" style="display: flex; gap: 10px; margin-bottom: 15px;">
                <button class="btn-like ${curtidoClasse}" style="border: 1px solid #ddd; padding: 5px 12px; border-radius: 15px; background: #fff; cursor: pointer;">
                    <i class="bi ${iconeLike}"></i> <span class="like-count">${post.likes || 0}</span>
                </button>
                <button class="btn-coment ${temComentarios ? 'comentario-ativo' : ''}" style="border: 1px solid #ddd; padding: 5px 12px; border-radius: 15px; background: #fff; cursor: pointer;">
                    <i class="bi bi-chat-dots"></i>
                </button>
            </div>
            
            <div class="secao-comentarios ${classeAtivo}" style="display: ${displaySecao};">
                <div class="lista-comentarios" style="margin-bottom: 10px;">
                    ${comentariosHTML}
                </div>
                <div class="campo-comentario-box" style="display: flex; align-items: center; gap: 10px; background-color: #f9f9f9; padding: 8px 15px; border-radius: 20px; margin-top: 10px;">
                    <i class="bi bi-emoji-smile btn-emoji" style="color: #777; font-size: 18px; cursor: pointer;"></i>
                    <input type="text" class="input-comentario" placeholder="Adicionar um comentário..." style="border: none; background: transparent; width: 100%; outline: none; font-size: 14px;">
                    <button class="btn-enviar-comentario" style="border: none; background: transparent; color: #e63946; cursor: pointer; padding: 0;">
                        <i class="bi bi-send-fill" style="font-size: 16px;"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

function abrirModalSugestao() {
    Swal.fire({
        title: '<strong>Nova Sugestão</strong>',
        html: `
            <form id="formSugestao">
                <div>
                    <textarea id="swal-descricao" class="swal2-textarea" placeholder="Descreva sua sugestão em detalhes..." style="width: 100%; margin: 0; height: 120px; resize: none;" required></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Enviar Sugestão',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e63c3c',
        cancelButtonColor: '#8c8c8c',
        focusConfirm: false,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const descricao = document.getElementById('swal-descricao').value.trim();

            if (!descricao) {
                Swal.showValidationMessage('Por favor, escreva sua sugestão!');
                return false;
            }

            const formData = new FormData();
            formData.append('descricao', descricao);

            // Ajustado para apontar para o local correto do backend de salvamento
            return fetch('../php/phpsugestoes/sugestao.php', {
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
            carregarSugestoes();
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: result.value.message || 'Sua sugestão foi cadastrada com sucesso.',
                confirmButtonColor: '#02d569'
            });
        }
    });
}

function atualizarFavoritas() {
    const containerFavoritas = document.querySelector('.painel-sugestoes .lista-items');
    if (!containerFavoritas) return;

    const posts = Array.from(document.querySelectorAll('.card-sugestao-post'));
    const dadosPosts = posts.map(post => {
        const nomeEl = post.querySelector('.autor-dados h3');
        const textoEl = post.querySelector('.secao-descricao p');
        const likeEl = post.querySelector('.like-count');

        return {
            nome: nomeEl ? nomeEl.textContent.trim() : 'Usuário',
            texto: textoEl ? textoEl.textContent.trim() : '',
            likes: likeEl ? parseInt(likeEl.textContent) || 0 : 0
        };
    });

    const postsCurtidos = dadosPosts.filter(post => post.likes > 0);
    postsCurtidos.sort((a, b) => b.likes - a.likes);
    const topPosts = postsCurtidos.slice(0, 5);

    if (topPosts.length === 0) {
        containerFavoritas.innerHTML = `<div style="padding: 15px; color: #999; font-size: 13px; text-align: center;">Nenhuma sugestão curtida ainda.</div>`;
        return;
    }

    containerFavoritas.innerHTML = topPosts.map((post, index) => {
        const resumoTexto = post.texto.length > 40 ? post.texto.substring(0, 40) + '...' : post.texto;
        const classeAtiva = index === 0 ? 'active' : '';

        return `
            <div class="item-sugestao ${classeAtiva}">
                <div class="avatar"></div>
                <div class="item-info">
                    <div class="item-header">
                        <span class="item-nome">${post.nome}</span>
                        <span class="item-tempo">${post.likes} <i class="bi bi-hand-thumbs-up-fill" style="font-size: 10px; color: #e63946;"></i></span>
                    </div>
                    <p class="item-resumo">${resumoTexto}</p>
                </div>
            </div>
        `;
    }).join('');
}