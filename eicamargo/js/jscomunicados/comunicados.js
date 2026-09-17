document.addEventListener('DOMContentLoaded', () => {
    const btnGeral = document.getElementById('btnNovoComunicadoGeral');

    if (btnGeral) {
        btnGeral.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalComunicadoGeral();
        });
    }
});

function abrirModalComunicadoGeral() {
    Swal.fire({
        title: '<strong>Novo Comunicado Geral</strong>',
        html: `
            <form id="formComunicadoModal" style="text-align: left; display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px;">Título:</label>
                    <input type="text" id="swal-titulo" class="swal2-input" placeholder="Título do comunicado geral" style="width: 100%; margin: 0; box-sizing: border-box;" required>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px;">Conteúdo:</label>
                    <textarea id="swal-conteudo" class="swal2-textarea" placeholder="Escreva o aviso para toda a escola..." style="width: 100%; margin: 0; height: 110px; resize: none; box-sizing: border-box;" required></textarea>
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

            if (!titulo || !conteudo) {
                Swal.showValidationMessage('Preencha o título e o conteúdo!');
                return false;
            }

            const formData = new FormData();
            formData.append('tipo_comunicado', 'geral');
            formData.append('titulo', titulo);
            formData.append('conteudo', conteudo);

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
                text: 'Comunicado Geral publicado com sucesso.',
                confirmButtonColor: '#02d569'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}