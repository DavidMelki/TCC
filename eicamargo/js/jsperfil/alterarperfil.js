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
    Swal.fire({
        title: '<h3 style="margin: 0; color: #333; font-size: 22px;">Editar Perfil</h3>',
        html: `
            <div style="text-align: left; display: flex; flex-direction: column; gap: 14px; padding: 10px 0;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px;">Foto de Perfil:</label>
                    <input type="file" id="swal-foto" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px;">Nome Completo:</label>
                    <input type="text" id="swal-nome" placeholder="Digite seu nome" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box;" value="Nome do Aluno">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: 600; color: #555; display: block; margin-bottom: 5px;">Descrição / Biografia / Curso:</label>
                    <textarea id="swal-bio" placeholder="Ex: Terceiro ano do curso Análise e Desenvolvimento de Sistemas." style="width: 100%; height: 90px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: none; box-sizing: border-box;">Terceiro ano do curso Análise e Desenvolvimento de Sistemas.</textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Salvar Alterações',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#6c757d',
        customClass: {
            popup: 'modal-perfil-personalizado'
        },
        preConfirm: () => {
            const foto = document.getElementById('swal-foto').files[0];
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
                confirmButtonColor: '#e63946',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    });
}