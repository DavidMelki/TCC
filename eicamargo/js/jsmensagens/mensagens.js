let usuarioAtivoId = null;

document.addEventListener('DOMContentLoaded', () => {
    carregarConversas();

    // Atualiza a lista de conversas automaticamente a cada 5 segundos para trazer mensagens novas
    setInterval(() => {
        carregarConversas(document.getElementById('inputPesquisaUsuario')?.value.trim() || '', false);
    }, 5000);

    const inputPesquisa = document.getElementById('inputPesquisaUsuario');
    const inputMensagem = document.getElementById('inputMensagem');
    const btnEnviar = document.getElementById('btnEnviar') || document.querySelector('.botao-enviar');

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

document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-menu-msg').forEach(menu => {
        menu.classList.remove('show');
    });
});

async function carregarConversas(termoBusca = '', recarregarTudo = true) {
    const container = document.getElementById('containerConversas');
    if (!container) return;

    try {
        const res = await fetch(`../php/phpmensagens/listar_conversas.php?busca=${encodeURIComponent(termoBusca)}`);
        const data = await res.json();

        if (data.status === 'success' && data.conversas.length > 0) {
            container.innerHTML = data.conversas.map(c => {
                const fotoUrl = c.foto_perfil ? `../uploads/${c.foto_perfil}` : '../uploads/default.png';
                const eAtiva = c.usuario_id === usuarioAtivoId ? 'ativa' : '';

                // Bolinha vermelha com a quantidade de mensagens não lidas
                const badgeNaoLidas = (c.nao_lidas > 0) 
                    ? `<span class="badge-nao-lidas">${c.nao_lidas}</span>` 
                    : '';

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
            container.innerHTML = `<p style="text-align: center; color: #999; margin-top: 20px;">Nenhum usuário encontrado.</p>`;
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
                            <p>${m.mensagem}</p>
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
        if (menu.id !== `dropdown-msg-${idMensagem}`) {
            menu.classList.remove('show');
        }
    });

    const menuAtual = document.getElementById(`dropdown-msg-${idMensagem}`);
    if (menuAtual) {
        menuAtual.classList.toggle('show');
    }
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
            if (divMensagem) {
                divMensagem.remove();
            }
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
    if (!input) return;

    const texto = input.value.trim();

    if (!texto) return;
    if (!usuarioAtivoId) {
        alert("Selecione um usuário para enviar a mensagem.");
        return;
    }

    const formData = new FormData();
    formData.append('destinatario_id', usuarioAtivoId);
    formData.append('mensagem', texto);

    try {
        const res = await fetch('../php/phpmensagens/enviar_mensagem.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.status === 'success') {
            abrirChat(usuarioAtivoId, document.getElementById('chatNome').textContent, document.getElementById('chatAvatar').style.backgroundImage.slice(5, -2));
            input.value = '';
        } else {
            alert('Erro ao enviar mensagem: ' + (data.message || 'Tente novamente.'));
        }
    } catch (e) {
        console.error('Erro ao enviar mensagem:', e);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    carregarConversas();

    const btnEmoji = document.getElementById('btnEmoji');
    const emojiPicker = document.getElementById('emojiPicker');
    const btnAnexo = document.getElementById('btnAnexo');
    const anexoMenu = document.getElementById('anexoMenu');
    const inputAnexo = document.getElementById('inputAnexo');
    const btnAnexoFoto = document.getElementById('btnAnexoFoto');
    const btnAnexoDoc = document.getElementById('btnAnexoDoc');
    const inputMensagem = document.getElementById('inputMensagem');

    // Toggle Menu de Emojis
    if (btnEmoji && emojiPicker) {
        btnEmoji.addEventListener('click', (e) => {
            e.stopPropagation();
            if (anexoMenu) anexoMenu.classList.remove('show');
            emojiPicker.classList.toggle('show');
        });

        // Clique em um emoji
        emojiPicker.querySelectorAll('span').forEach(emojiSpan => {
            emojiSpan.addEventListener('click', () => {
                if (inputMensagem) {
                    inputMensagem.value += emojiSpan.textContent;
                    inputMensagem.focus();
                }
            });
        });
    }

    // Toggle Menu de Anexos
    if (btnAnexo && anexoMenu) {
        btnAnexo.addEventListener('click', (e) => {
            e.stopPropagation();
            if (emojiPicker) emojiPicker.classList.remove('show');
            anexoMenu.classList.toggle('show');
        });
    }

    // Ações do Menu de Anexos
    if (btnAnexoFoto && inputAnexo) {
        btnAnexoFoto.addEventListener('click', () => {
            inputAnexo.setAttribute('accept', 'image/*,video/*');
            inputAnexo.click();
            anexoMenu.classList.remove('show');
        });
    }

    if (btnAnexoDoc && inputAnexo) {
        btnAnexoDoc.addEventListener('click', () => {
            inputAnexo.setAttribute('accept', '.pdf,.doc,.docx,.xls,.xlsx,.txt');
            inputAnexo.click();
            anexoMenu.classList.remove('show');
        });
    }

    // Evento para quando o usuário seleciona um ficheiro/anexo
    if (inputAnexo) {
        inputAnexo.addEventListener('change', () => {
            if (inputAnexo.files.length > 0) {
                const arquivo = inputAnexo.files[0];
                alert(`Arquivo selecionado: ${arquivo.name}`);
                // Aqui pode implementar o envio imediato ou upload do arquivo via FormData
            }
        });
    }

    // Fechar menus ao clicar fora
    document.addEventListener('click', () => {
        if (emojiPicker) emojiPicker.classList.remove('show');
        if (anexoMenu) anexoMenu.classList.remove('show');
        document.querySelectorAll('.dropdown-menu-msg').forEach(menu => {
            menu.classList.remove('show');
        });
    });
});