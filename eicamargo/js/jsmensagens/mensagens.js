let usuarioAtivoId = null;
let filtroAtual = 'todas'; // Controla qual filtro está ativo ('todas' ou 'nao_lidas')

document.addEventListener('DOMContentLoaded', () => {
    carregarConversas();

    // Atualiza a lista de conversas a cada 5 segundos
    setInterval(() => {
        carregarConversas(document.getElementById('inputPesquisaUsuario')?.value.trim() || '', false);
    }, 5000);

    const inputPesquisa = document.getElementById('inputPesquisaUsuario');
    const inputMensagem = document.getElementById('inputMensagem');
    const btnEnviar = document.getElementById('btnEnviar');
    const btnEmoji = document.getElementById('btnEmoji');
    const emojiPicker = document.getElementById('emojiPicker');
    const btnAnexo = document.getElementById('btnAnexo');
    const inputAnexo = document.getElementById('inputAnexo');
    const btnFiltroTodas = document.getElementById('btnFiltroTodas');
    const btnFiltroNaoLidas = document.getElementById('btnFiltroNaoLidas');

    // Evento do botão "Todas"
    if (btnFiltroTodas) {
        btnFiltroTodas.addEventListener('click', () => {
            filtroAtual = 'todas';
            btnFiltroTodas.classList.add('ativo');
            if (btnFiltroNaoLidas) btnFiltroNaoLidas.classList.remove('ativo');
            carregarConversas(document.getElementById('inputPesquisaUsuario')?.value.trim() || '');
        });
    }

    // Evento do botão "Não lidas"
    if (btnFiltroNaoLidas) {
        btnFiltroNaoLidas.addEventListener('click', () => {
            filtroAtual = 'nao_lidas';
            btnFiltroNaoLidas.classList.add('ativo');
            if (btnFiltroTodas) btnFiltroTodas.classList.remove('ativo');
            carregarConversas(document.getElementById('inputPesquisaUsuario')?.value.trim() || '');
        });
    }

    // Emoji Picker Toggle
    if (btnEmoji && emojiPicker) {
        btnEmoji.addEventListener('click', (e) => {
            e.stopPropagation();
            const estaVisivel = emojiPicker.style.display === 'flex';
            emojiPicker.style.display = estaVisivel ? 'none' : 'flex';
        });

        emojiPicker.querySelectorAll('span').forEach(emojiSpan => {
            emojiSpan.addEventListener('click', () => {
                if (inputMensagem) {
                    inputMensagem.value += emojiSpan.textContent;
                    inputMensagem.focus();
                }
            });
        });
    }

    // Clique no clipe abre seleção de imagem/arquivo
    if (btnAnexo && inputAnexo) {
        btnAnexo.addEventListener('click', (e) => {
            e.stopPropagation();
            inputAnexo.setAttribute('accept', 'image/*,.pdf,.doc,.docx');
            inputAnexo.click();
        });
    }

    // Ao selecionar arquivo, envia a mensagem com o anexo automaticamente
    if (inputAnexo) {
        inputAnexo.addEventListener('change', () => {
            if (inputAnexo.files.length > 0) {
                enviarMensagem();
            }
        });
    }

    if (inputPesquisa) {
        inputPesquisa.addEventListener('input', () => {
            carregarConversas(inputPesquisa.value.trim());
        });
    }

    if (btnEnviar) {
        btnEnviar.addEventListener('click', (e) => {
            e.preventDefault();
            enviarMensagem();
        });
    }

    if (inputMensagem) {
        inputMensagem.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                enviarMensagem();
            }
        });
    }
});

// Oculta menus ao clicar fora
document.addEventListener('click', () => {
    const emojiPicker = document.getElementById('emojiPicker');
    if (emojiPicker) emojiPicker.style.display = 'none';
    document.querySelectorAll('.dropdown-menu-msg').forEach(menu => menu.classList.remove('show'));
});

async function carregarConversas(termoBusca = '', recarregarTudo = true) {
    const container = document.getElementById('containerConversas');
    if (!container) return;

    try {
        // Passa o termo de busca e o filtro selecionado ('todas' ou 'nao_lidas')
        const res = await fetch(`../php/phpmensagens/listar_conversas.php?busca=${encodeURIComponent(termoBusca)}&filtro=${filtroAtual}`);
        const data = await res.json();

        if (data.status === 'success' && data.conversas.length > 0) {
            container.innerHTML = data.conversas.map(c => {
                const fotoUrl = c.foto_perfil ? `../uploads/${c.foto_perfil}` : '../uploads/default.png';
                const eAtiva = c.usuario_id === usuarioAtivoId ? 'ativa' : '';
                const badgeNaoLidas = (c.nao_lidas > 0) ? `<span class="badge-nao-lidas">${c.nao_lidas}</span>` : '';

                return `
                    <div class="conversa ${eAtiva}" data-id="${c.usuario_id}" data-nome="${c.nome}" data-foto="${fotoUrl}">
                        <div class="avatar-post" style="background-image: url('${fotoUrl}'); background-size: cover; background-position: center;"></div>
                        <div class="texto">
                            <h4>${c.nome}</h4>
                            <p>${c.ultima_msg}</p>
                        </div>
                        <div class="conversa-status">
                            <span>${c.tempo}</span>
                            ${badgeNaoLidas}
                        </div>
                    </div>
                `;
            }).join('');

            document.querySelectorAll('.conversa').forEach(el => {
                el.addEventListener('click', () => {
                    document.querySelectorAll('.conversa').forEach(i => i.classList.remove('ativa'));
                    el.classList.add('ativa');

                    usuarioAtivoId = parseInt(el.getAttribute('data-id'));
                    const nome = el.getAttribute('data-nome');
                    const foto = el.getAttribute('data-foto');

                    abrirChat(usuarioAtivoId, nome, foto);
                });
            });

        } else if (recarregarTudo) {
            container.innerHTML = `<p style="text-align: center; color: #999; margin-top: 20px;">Nenhuma conversa encontrada.</p>`;
        }
    } catch (e) {
        console.error('Erro ao buscar conversas:', e);
    }
}

async function abrirChat(id, nome, foto) {
    const chatTopo = document.getElementById('chatTopo');
    const boxEnviar = document.getElementById('boxEnviar');

    if (chatTopo) chatTopo.style.visibility = 'visible';
    if (boxEnviar) boxEnviar.style.visibility = 'visible';

    const chatNome = document.getElementById('chatNome');
    const chatTag = document.getElementById('chatTag');
    const chatAvatar = document.getElementById('chatAvatar');

    if (chatNome) chatNome.textContent = nome;
    if (chatTag) chatTag.textContent = '@' + nome.toLowerCase().replace(/\s+/g, '');
    if (chatAvatar) {
        chatAvatar.style.backgroundImage = `url('${foto}')`;
        chatAvatar.style.backgroundSize = 'cover';
    }

    const containerMsg = document.getElementById('containerMensagens');
    if (!containerMsg) return;

    try {
        const res = await fetch(`../php/phpmensagens/carregar_mensagens.php?usuario_id=${id}`);
        const data = await res.json();

        if (data.status === 'success') {
            if (data.mensagens && data.mensagens.length > 0) {
                containerMsg.innerHTML = data.mensagens.map(m => {
                    const eMinha = (parseInt(m.remetente_id) === data.meu_id);
                    const tipo = eMinha ? 'enviada' : 'recebida';
                    
                    let htmlAnexo = '';
                    if (m.anexo) {
                        const caminhoAnexo = `../uploads/anexos/${m.anexo}`;
                        if (m.anexo.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
                            htmlAnexo = `<div class="msg-imagem"><a href="${caminhoAnexo}" target="_blank"><img src="${caminhoAnexo}" style="max-width: 200px; border-radius: 8px; margin-bottom: 5px; display: block;"></a></div>`;
                        } else {
                            htmlAnexo = `<div class="msg-documento"><a href="${caminhoAnexo}" target="_blank" style="color: inherit; text-decoration: underline;"><i class="fa-solid fa-paperclip"></i> Ver Anexo</a></div>`;
                        }
                    }

                    const menuOpcoes = eMinha ? `
                        <div class="msg-opcoes">
                            <button class="btn-tres-pontos" onclick="toggleMenuMsg(event, ${m.id})">⋮</button>
                            <div id="dropdown-msg-${m.id}" class="dropdown-menu-msg">
                                <button class="dropdown-item-msg" onclick="apagarMensagem(${m.id}, this)">
                                    <i class="fa-solid fa-trash"></i> Apagar
                                </button>
                            </div>
                        </div>
                    ` : '';

                    return `
                        <div class="msg ${tipo}" data-msg-id="${m.id}">
                            ${htmlAnexo}
                            ${m.mensagem ? `<p>${m.mensagem}</p>` : ''}
                            <div class="hora-container">
                                <span class="hora">${m.hora}</span>
                                ${menuOpcoes}
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                containerMsg.innerHTML = `<p style="text-align: center; color: #aaa; margin-top: 50px;">Nenhuma mensagem anterior. Envie um "Olá" para começar!</p>`;
            }
            containerMsg.scrollTop = containerMsg.scrollHeight;
            carregarConversas(document.getElementById('inputPesquisaUsuario')?.value.trim() || '', false);
        }
    } catch (e) {
        console.error('Erro ao carregar mensagens:', e);
    }
}

function toggleMenuMsg(event, idMensagem) {
    event.stopPropagation();
    document.querySelectorAll('.dropdown-menu-msg').forEach(menu => {
        if (menu.id !== `dropdown-msg-${idMensagem}`) menu.classList.remove('show');
    });

    const menuAtual = document.getElementById(`dropdown-msg-${idMensagem}`);
    if (menuAtual) menuAtual.classList.toggle('show');
}

async function apagarMensagem(idMensagem, elementoBotao) {
    if (!confirm("Deseja apagar esta mensagem?")) return;

    const formData = new FormData();
    formData.append('mensagem_id', idMensagem);

    try {
        const res = await fetch('../php/phpmensagens/apagar_mensagem.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.status === 'success') {
            const divMensagem = elementoBotao.closest('.msg');
            if (divMensagem) divMensagem.remove();
            carregarConversas('', false);
        } else {
            alert(data.message || 'Erro ao apagar mensagem.');
        }
    } catch (e) {
        console.error('Erro ao apagar mensagem:', e);
    }
}

async function enviarMensagem() {
    const input = document.getElementById('inputMensagem');
    const inputAnexo = document.getElementById('inputAnexo');
    if (!input) return;

    const texto = input.value.trim();
    const temAnexo = inputAnexo && inputAnexo.files.length > 0;

    if (!texto && !temAnexo) return;
    if (!usuarioAtivoId) {
        alert("Selecione um usuário para enviar a mensagem.");
        return;
    }

    const formData = new FormData();
    formData.append('destinatario_id', usuarioAtivoId);
    formData.append('mensagem', texto);

    if (temAnexo) {
        formData.append('anexo', inputAnexo.files[0]);
    }

    try {
        const res = await fetch('../php/phpmensagens/enviar_mensagem.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.status === 'success') {
            input.value = '';
            if (inputAnexo) inputAnexo.value = '';
            abrirChat(usuarioAtivoId, document.getElementById('chatNome').textContent, document.getElementById('chatAvatar').style.backgroundImage.slice(5, -2));
        } else {
            alert('Erro ao enviar mensagem: ' + (data.message || 'Tente novamente.'));
        }
    } catch (e) {
        console.error('Erro ao enviar mensagem:', e);
    }
}

// Abre direto o chat com o vendedor quando vier de "Reservar" (vendas.php)
document.addEventListener('DOMContentLoaded', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const usuarioIdParam = parseInt(urlParams.get('usuario_id'));
    const produtoParam = urlParams.get('produto'); // já vem decodificado

    if (!usuarioIdParam) return;

    try {
        // Busca os dados do vendedor, mesmo que nunca tenham conversado
        const res = await fetch(`../php/phpmensagens/buscar_usuario.php?usuario_id=${usuarioIdParam}`);
        const data = await res.json();

        if (data.status !== 'success') {
            alert(data.message || 'Não foi possível abrir a conversa.');
            return;
        }

        const u = data.usuario;
        const foto = u.foto_perfil ? `../uploads/${u.foto_perfil}` : '../uploads/default.png';

        // IMPORTANTE: define o usuário ativo para o envio funcionar
        usuarioAtivoId = u.usuario_id;
        await abrirChat(u.usuario_id, u.nome, foto);

        // Preenche o texto de reserva
        if (produtoParam) {
            const inputMensagem = document.getElementById('inputMensagem');
            if (inputMensagem) {
                inputMensagem.value = `Olá! Gostaria de reservar o item: ${produtoParam}`;
                inputMensagem.focus();
            }
        }
    } catch (e) {
        console.error('Erro ao abrir conversa do vendedor:', e);
    }
});