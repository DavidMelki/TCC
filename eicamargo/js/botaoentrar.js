document.addEventListener('DOMContentLoaded', () => {
    // 1. LÓGICA DO BOTÃO ENTRAR (LOGIN)
    const formLogin = document.getElementById('form-login');
    if (formLogin) {
        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value.trim();

            if (!email || !senha) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Preencha o e-mail e a senha para entrar!'
                });
                return;
            }

            const formData = new FormData();
            formData.append('email', email);
            formData.append('senha', senha);

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
                        window.location.href = 'pags/sugestoes.php';
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
    // Procura pelo botão de cadastro usando a classe ou texto dele
    const btnCadastro = document.querySelector('.botao-cadastro') || Array.from(document.querySelectorAll('button')).find(el => el.textContent.includes('Registrar-se'));

    if (btnCadastro) {
        btnCadastro.addEventListener('click', (e) => {
            e.preventDefault();
            abrirModalCadastro();
        });
    }
});

// Função que abre a caixinha de cadastro na tela
function abrirModalCadastro() {
    Swal.fire({
        title: 'Criar Nova Conta',
        html: `
            <form id="formCadastro" style="text-align: left; display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px;">Nome completo:</label>
                    <input type="text" id="swal-nome" class="swal2-input" placeholder="Seu nome" style="margin: 0; width: 100%; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px;">E-mail:</label>
                    <input type="email" id="swal-email" class="swal2-input" placeholder="seu@email.com" style="margin: 0; width: 100%; box-sizing: border-box;">
                </div>
                <div>
                    <label style="font-size: 13px; font-weight: bold; display: block; margin-bottom: 4px;">Senha:</label>
                    <input type="password" id="swal-senha" class="swal2-input" placeholder="Sua senha" style="margin: 0; width: 100%; box-sizing: border-box;">
                </div>
            </form>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Cadastrar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d32f2f',
        preConfirm: () => {
            const nome = document.getElementById('swal-nome').value.trim();
            const email = document.getElementById('swal-email').value.trim();
            const senha = document.getElementById('swal-senha').value.trim();

            if (!nome || !email || !senha) {
                Swal.showValidationMessage('Preencha todos os campos!');
                return false;
            }

            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('email', email);
            formData.append('senha', senha);

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
                text: 'Cadastro realizado com sucesso! Agora faça o login.'
            });
        }
    });
}