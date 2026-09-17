document.addEventListener('DOMContentLoaded', () => {
    // 1. LÓGICA DO BOTÃO ENTRAR (LOGIN)
    const formLogin = document.getElementById('form-login');
    if (formLogin) {
        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();

            const tipo = document.getElementById('tipo_usuario') ? document.getElementById('tipo_usuario').value : 'aluno';
            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();
            const codigo = document.getElementById('codigo_curso') ? document.getElementById('codigo_curso').value.trim() : '';

            if (!email || !senha || !codigo) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Preencha o e-mail, senha e o código de acesso para entrar!'
                });
                return;
            }

            const formData = new FormData();
            formData.append('tipo', tipo);
            formData.append('email', email);
            formData.append('senha', senha);
            formData.append('codigo_curso', codigo);

            fetch('php/phplogin/login.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Bem-vindo!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = 'pags/comunicados.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao entrar',
                        text: data.message
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível conectar ao servidor.'
                });
            });
        });
    }

    // 2. LÓGICA DO BOTÃO REGISTRAR-SE (CADASTRO)
    const btnCadastro = document.querySelector('.botao-cadastro') || Array.from(document.querySelectorAll('button')).find(el => el.textContent.includes('Registrar-se'));

    if (btnCadastro) {
        btnCadastro.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalCadastro();
        });
    }
});

// Função que abre a janela de cadastro na tela
function abrirModalCadastro() {
    Swal.fire({
        title: '<strong style="font-size: 20px; color: #1a1a1a;">Criar Nova Conta</strong>',
        html: `
            <form id="formCadastro" style="text-align: left; display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px; color: #1a1a1a;">Nome completo:</label>
                    <input type="text" id="swal-nome" class="swal2-input" placeholder="Seu nome" style="width: 100%; margin: 0; box-sizing: border-box; height: 42px; border-radius: 8px; font-size: 14px;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px; color: #1a1a1a;">E-mail:</label>
                    <input type="email" id="swal-email" class="swal2-input" placeholder="seu@email.com" style="width: 100%; margin: 0; box-sizing: border-box; height: 42px; border-radius: 8px; font-size: 14px;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px; color: #1a1a1a;">Senha:</label>
                    <input type="password" id="swal-senha" class="swal2-input" placeholder="Sua senha" style="width: 100%; margin: 0; box-sizing: border-box; height: 42px; border-radius: 8px; font-size: 14px;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px; color: #1a1a1a;">Tipo de Conta:</label>
                    <select id="swal-tipo" class="swal2-select" style="width: 100%; margin: 0; box-sizing: border-box; height: 42px; border-radius: 8px; font-size: 14px; border: 1px solid #d9d9d9; padding: 0 10px;">
                        <option value="aluno">Aluno</option>
                        <option value="coordenador">Coordenador</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px; color: #1a1a1a;">Código do Curso:</label>
                    <input type="text" id="swal-codigo" class="swal2-input" placeholder="Digite o código fornecido" style="width: 100%; margin: 0; box-sizing: border-box; height: 42px; border-radius: 8px; font-size: 14px;">
                </div>
            </form>
        `,
        customClass: {
            popup: 'swal2-modal-arredondado'
        },
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Cadastrar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#e63c3c',
        cancelButtonColor: '#8c8c8c',
        preConfirm: () => {
            const nome = document.getElementById('swal-nome').value.trim();
            const email = document.getElementById('swal-email').value.trim();
            const senha = document.getElementById('swal-senha').value.trim();
            const tipo = document.getElementById('swal-tipo').value;
            const codigo = document.getElementById('swal-codigo').value.trim();

            if (!nome || !email || !senha || !codigo) {
                Swal.showValidationMessage('Preencha todos os campos, incluindo o código!');
                return false;
            }

            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('email', email);
            formData.append('senha', senha);
            formData.append('tipo', tipo);
            formData.append('codigo', codigo);

            return fetch('php/phpcadastro/cadastrar.php', {
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
                Swal.showValidationMessage(`Erro: ${error.message}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: 'Cadastro realizado com sucesso! Agora faça o login.',
                confirmButtonColor: '#02d569'
            });
        }
    });
}