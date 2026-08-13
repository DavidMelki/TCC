document.getElementById('btnNovaSugestao').addEventListener('click', function () {
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
            const descricao = document.getElementById('swal-descricao').value;

            if (!descricao) {
                Swal.showValidationMessage('Por favor, escreva sua sugestão!');
                return false;
            }

            const formData = new FormData();
            formData.append('descricao', descricao);

            return fetch('sugestao.php', {
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
                title: 'Sucesso!',
                text: result.value.message,
                confirmButtonColor: '#02d569'
            });
        }
    });
});