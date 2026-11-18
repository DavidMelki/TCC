document.addEventListener('DOMContentLoaded', () => {
    const btnCurso = document.getElementById('btnNovoComunicadoCurso');

    if (btnCurso) {
        btnCurso.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalComunicadoCurso();
        });
    }
});

function escaparHTML(texto) {
    return String(texto ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

async function abrirModalComunicadoCurso() {
    // Só o diretor recebe a lista de cursos; para os demais o seletor não aparece
    let cursos = [];
    let cursoPadrao = 0;

    try {
        const res = await fetch('../php/phpcomunicados/listar_cursos.php');
        const data = await res.json();
        if (data.status === 'success') {
            cursos = data.cursos || [];
            cursoPadrao = data.curso_padrao || 0;
        }
    } catch (e) {
        // Sem a lista, publica no curso do próprio usuário
    }

    // Se o diretor está vendo um curso na página, já deixa esse curso selecionado
    const cursoDaUrl = parseInt(new URLSearchParams(window.location.search).get('curso_id')) || 0;
    const cursoPreSelecionado = cursoDaUrl || cursoPadrao;

    const seletorCurso = cursos.length > 0 ? `
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px;">Curso:</label>
                    <select id="swal-curso-destino" class="swal2-select" style="width: 100%; margin: 0; box-sizing: border-box; height: 42px; border-radius: 8px; font-size: 14px; border: 1px solid #d9d9d9; padding: 0 10px;">
                        <option value="">Selecione o curso</option>
                        ${cursos.map(c => `<option value="${c.id}" ${c.id === cursoPreSelecionado ? 'selected' : ''}>${escaparHTML(c.nome)}</option>`).join('')}
                    </select>
                </div>
    ` : '';

    let cursoEscolhido = '';

    Swal.fire({
        title: '<strong>Novo Comunicado do Curso</strong>',
        html: `
            <form id="formComunicadoModal" style="text-align: left; display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
                ${seletorCurso}
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px;">Título:</label>
                    <input type="text" id="swal-titulo" class="swal2-input" placeholder="Título do comunicado do curso" style="width: 100%; margin: 0; box-sizing: border-box;" required>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px;">Conteúdo:</label>
                    <textarea id="swal-conteudo" class="swal2-textarea" placeholder="Escreva o comunicado para a sua turma/curso..." style="width: 100%; margin: 0; height: 110px; resize: none; box-sizing: border-box;" required></textarea>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Publicar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e63c3c',
        cancelButtonColor: '#8c8c8c',
        focusConfirm: false,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const titulo = document.getElementById('swal-titulo').value.trim();
            const conteudo = document.getElementById('swal-conteudo').value.trim();
            const selectCurso = document.getElementById('swal-curso-destino');

            if (selectCurso && !selectCurso.value) {
                Swal.showValidationMessage('Selecione o curso do comunicado!');
                return false;
            }

            if (!titulo || !conteudo) {
                Swal.showValidationMessage('Preencha o título e o conteúdo!');
                return false;
            }

            const formData = new FormData();
            formData.append('tipo_comunicado', 'curso');
            formData.append('titulo', titulo);
            formData.append('conteudo', conteudo);

            if (selectCurso) {
                cursoEscolhido = selectCurso.value;
                formData.append('curso_id', cursoEscolhido);
            }

            return fetch('../php/phpcomunicados/salvar_comunicado.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    throw new Error(data.message || 'Erro ao publicar.');
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`Erro: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'Comunicado do Curso publicado com sucesso.',
                confirmButtonColor: '#02d569'
            }).then(() => {
                // O diretor vai direto para o curso em que publicou
                if (cursoEscolhido) {
                    window.location.href = 'curso.php?curso_id=' + encodeURIComponent(cursoEscolhido);
                } else {
                    window.location.reload();
                }
            });
        }
    });
}