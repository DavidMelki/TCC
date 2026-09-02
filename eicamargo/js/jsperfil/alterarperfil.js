document.addEventListener('DOMContentLoaded', () => {
    const btnEditar = document.querySelector('.btn-editar');

    if (btnEditar) {
        btnEditar.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalEditarPerfil();
        });
    }
});

function abrirModalEditarPerfil() {
    const nomeAtual = document.querySelector('.info-perfil h2')?.textContent.trim() || '';
    const bioElemento = document.querySelector('.info-perfil .descricao');
    const bioTexto = bioElemento ? bioElemento.textContent.trim() : '';
    const bioAtual = (bioTexto === 'Nenhuma biografia informada.') ? '' : bioTexto;

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

                <!-- Biografia -->
                <div>
                  <label style="font-size: 13px; font-weight: 600; color: #444; display: block; margin-bottom: 6px;">Descrição / Biografia / Curso</label>
                  <textarea id="swal-bio" placeholder="Ex: Terceiro ano do curso Análise e Desenvolvimento de Sistemas." style="width: 100%; height: 80px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: none; box-sizing: border-box; font-family: inherit;">${bioAtual}</textarea>
                </div>

            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Salvar Alterações',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e63c3c',
        cancelButtonColor: '#8c8c8c',
        preConfirm: () => {
            const foto = document.getElementById('swal-foto').files[0];
            const capa = document.getElementById('swal-capa').files[0];
            const nome = document.getElementById('swal-nome').value.trim();
            const biografia = document.getElementById('swal-bio').value.trim();

            if (!nome) {
                Swal.showValidationMessage('O nome completo não pode ficar vazio.');
                return false;
            }

            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('biografia', biografia);
            if (foto) formData.append('foto', foto);
            if (capa) formData.append('capa', capa);

            return fetch('../php/phpperfil/atualizarperfil.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        throw new Error(data.message);
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(`Erro ao atualizar: ${error.message}`);
                });
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