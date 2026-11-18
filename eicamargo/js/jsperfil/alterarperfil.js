document.addEventListener('DOMContentLoaded', () => {
    const btnEditar = document.querySelector('.btn-editar');

    if (btnEditar) {
        btnEditar.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalEditarPerfil();
        });
    }
});

// Escapa texto para usar dentro de atributos/HTML do modal
function escaparAttr(texto) {
    return String(texto ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Exemplos de função na escola, de acordo com o tipo de conta
const EXEMPLOS_FUNCAO = {
    professor: 'Ex: Professor de Matemática',
    coordenador: 'Ex: Coordenador do curso de Desenvolvimento de Sistemas',
    diretor: 'Ex: Diretor da escola'
};

function abrirModalEditarPerfil() {
    const info = document.querySelector('.info-perfil');

    const tipo = info?.dataset.tipo || 'aluno';
    const eAluno = (tipo === 'aluno');

    const nomeAtual = escaparAttr(info?.querySelector('h2')?.textContent.trim() || '');
    const anoAtual = info?.dataset.ano || '';
    const cursoAtual = escaparAttr(info?.dataset.curso || '');
    const funcaoAtual = escaparAttr(info?.dataset.funcao || '');
    const bioAtual = escaparAttr(info?.dataset.bio || '');
    const exemploFuncao = escaparAttr(EXEMPLOS_FUNCAO[tipo] || 'Ex: Professor de Matemática');

    // Aluno informa ano e curso; os demais informam a função na escola
    const camposIdentificacao = eAluno ? `
                <!-- Ano e Curso -->
                <div style="display: flex; gap: 10px;">
                    <div style="width: 38%;">
                        <label style="font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px;">Ano *</label>
                        <select id="swal-ano" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box; background: #fff; font-family: inherit;">
                            <option value="" ${anoAtual === '' || anoAtual === '0' ? 'selected' : ''}>Selecione</option>
                            <option value="1" ${anoAtual === '1' ? 'selected' : ''}>1º ano</option>
                            <option value="2" ${anoAtual === '2' ? 'selected' : ''}>2º ano</option>
                            <option value="3" ${anoAtual === '3' ? 'selected' : ''}>3º ano</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px;">Curso *</label>
                        <input type="text" id="swal-curso" maxlength="100" placeholder="Ex: Desenvolvimento de Sistemas" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box;" value="${cursoAtual}">
                    </div>
                </div>
    ` : `
                <!-- Função na escola -->
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px;">O que você exerce na escola *</label>
                    <input type="text" id="swal-funcao" maxlength="100" placeholder="${exemploFuncao}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box;" value="${funcaoAtual}">
                </div>
    `;

    Swal.fire({
        title: '<h3 style="margin: 0; color: #111; font-size: 22px; font-weight: 700;">Editar Perfil</h3>',
        html: `
            <style>
                .swal2-file-custom {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    background: #f8f9fa;
                    border: 1px dashed #ccc;
                    border-radius: 8px;
                    padding: 8px 12px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                }
                .swal2-file-custom:hover {
                    border-color: #e63c3c;
                    background: #fff5f5;
                }
                .swal2-file-btn {
                    background: #e63c3c;
                    color: #fff;
                    font-size: 12px;
                    font-weight: 600;
                    padding: 6px 12px;
                    border-radius: 6px;
                    pointer-events: none;
                }
                .swal2-file-name {
                    font-size: 13px;
                    color: #666;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 220px;
                }
            </style>

            <div style="text-align: left; display: flex; flex-direction: column; gap: 14px; padding: 10px 0;">
                
                <!-- Foto de Perfil -->
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px;">Foto de Perfil</label>
                    <label for="swal-foto" class="swal2-file-custom">
                        <span class="swal2-file-btn"><i class="bi bi-image" style="margin-right: 4px;"></i> Escolher Foto</span>
                        <span id="nome-foto" class="swal2-file-name">Nenhum arquivo selecionado</span>
                    </label>
                    <input type="file" id="swal-foto" accept="image/*" style="display: none;" onchange="atualizarNomeArquivo(this, 'nome-foto')">
                </div>

                <!-- Imagem de Capa -->
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px;">Imagem de Capa (Banner)</label>
                    <label for="swal-capa" class="swal2-file-custom">
                        <span class="swal2-file-btn"><i class="bi bi-card-image" style="margin-right: 4px;"></i> Escolher Capa</span>
                        <span id="nome-capa" class="swal2-file-name">Nenhum arquivo selecionado</span>
                    </label>
                    <input type="file" id="swal-capa" accept="image/*" style="display: none;" onchange="atualizarNomeArquivo(this, 'nome-capa')">
                </div>

                <!-- Nome -->
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px;">Nome Completo</label>
                    <input type="text" id="swal-nome" placeholder="Digite seu nome" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box;" value="${nomeAtual}">
                </div>

                ${camposIdentificacao}

                <!-- Biografia (opcional) -->
                <div>
                  <label style="font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px;">Biografia <span style="font-weight: 400; color: #8c8c8c;">(opcional)</span></label>
                  <textarea id="swal-bio" maxlength="200" placeholder="Escreva uma frase sobre você, se quiser." style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: none; box-sizing: border-box; font-family: inherit;">${bioAtual}</textarea>
                </div>

            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Salvar Alterações',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e63c3c',
        cancelButtonColor: '#8c8c8c',
        preConfirm: async () => {
            const fotoOriginal = document.getElementById('swal-foto').files[0];
            const capaOriginal = document.getElementById('swal-capa').files[0];
            const nome = document.getElementById('swal-nome').value.trim();
            const biografia = document.getElementById('swal-bio').value.trim();

            if (!nome) {
                Swal.showValidationMessage('O nome completo não pode ficar vazio.');
                return false;
            }

            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('biografia', biografia);

            if (eAluno) {
                const ano = document.getElementById('swal-ano').value;
                const curso = document.getElementById('swal-curso').value.trim();

                if (!ano) {
                    Swal.showValidationMessage('Selecione o seu ano (1º, 2º ou 3º).');
                    return false;
                }
                if (!curso) {
                    Swal.showValidationMessage('Informe o seu curso.');
                    return false;
                }

                formData.append('ano', ano);
                formData.append('curso', curso);
            } else {
                const funcao = document.getElementById('swal-funcao').value.trim();

                if (!funcao) {
                    Swal.showValidationMessage('Informe o que você exerce na escola.');
                    return false;
                }

                formData.append('funcao', funcao);
            }

            try {
                const foto = fotoOriginal ? await comprimirImagem(fotoOriginal, 800) : null;
                const capa = capaOriginal ? await comprimirImagem(capaOriginal, 1600) : null;

                if (foto) formData.append('foto', foto);
                if (capa) formData.append('capa', capa);

                const res = await fetch('../php/phpperfil/atualizarperfil.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status === 'error') {
                    throw new Error(data.message);
                }
                return data;
            } catch (error) {
                Swal.showValidationMessage(`Erro ao atualizar: ${error.message}`);
                return false;
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Tudo pronto!',
                text: 'Seu perfil foi atualizado com sucesso.',
                confirmButtonColor: '#e63c3c',
                timer: 1800,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}

function atualizarNomeArquivo(input, idLabel) {
    const span = document.getElementById(idLabel);
    if (input.files && input.files.length > 0) {
        span.textContent = input.files[0].name;
        span.style.color = '#333';
        span.style.fontWeight = '600';
    } else {
        span.textContent = 'Nenhum arquivo selecionado';
        span.style.color = '#666';
        span.style.fontWeight = 'normal';
    }
}

// Redimensiona e comprime a imagem no navegador (devolve um File menor)
function comprimirImagem(arquivo, ladoMaximo) {
    return new Promise((resolve) => {
        // Imagens pequenas e GIFs (animação) seguem como estão
        if (arquivo.size <= 1024 * 1024 || arquivo.type === 'image/gif') {
            return resolve(arquivo);
        }

        const url = URL.createObjectURL(arquivo);
        const img = new Image();

        img.onload = () => {
            URL.revokeObjectURL(url);

            const escala = Math.min(1, ladoMaximo / Math.max(img.width, img.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(img.width * escala);
            canvas.height = Math.round(img.height * escala);

            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#ffffff'; // fundo branco (JPG não tem transparência)
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

            canvas.toBlob((blob) => {
                if (!blob) return resolve(arquivo);
                const nomeBase = arquivo.name.replace(/\.[^.]+$/, '');
                resolve(new File([blob], nomeBase + '.jpg', { type: 'image/jpeg' }));
            }, 'image/jpeg', 0.85);
        };

        // Se o navegador não conseguir ler a imagem (ex.: HEIC), envia o original
        img.onerror = () => {
            URL.revokeObjectURL(url);
            resolve(arquivo);
        };

        img.src = url;
    });
}