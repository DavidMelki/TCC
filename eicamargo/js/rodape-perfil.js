document.addEventListener('DOMContentLoaded', () => {

    // Menu lateral: clicar em qualquer parte do item (não só no texto) navega
    document.querySelectorAll('.nav-menu li').forEach(li => {
        const link = li.querySelector('a');
        if (!link) return;

        li.style.cursor = 'pointer';
        li.addEventListener('click', (e) => {
            if (e.target.closest('a')) return; // se clicou no próprio link, ele já navega
            link.click();
        });
    });

    const footer = document.querySelector('.profile-footer');
    if (!footer) return;

    const info = footer.querySelector('.profile-info');
    const dots = footer.querySelector('.bi-three-dots');

    // Estilo do menu (injetado aqui para valer em todas as páginas)
    const style = document.createElement('style');
    style.textContent = `
        .profile-footer { position: relative; }
        .menu-perfil-footer {
            display: none;
            position: absolute;
            bottom: calc(100% + 6px);
            right: 8px;
            min-width: 140px;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
            padding: 6px;
            z-index: 1000;
        }
        .menu-perfil-footer.show { display: block; }
        .menu-perfil-sair {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            border: none;
            background: transparent;
            color: #e63946;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
        }
        .menu-perfil-sair:hover { background: #fdecee; }
    `;
    document.head.appendChild(style);

    // Clicar em qualquer parte do rodapé (área cinza) leva para o perfil
    footer.style.cursor = 'pointer';
    footer.title = 'Ver perfil';
    footer.addEventListener('click', (e) => {
        // Ignora cliques nos 3 pontos e no menu "Sair"
        if (e.target.closest('.bi-three-dots') || e.target.closest('.menu-perfil-footer')) return;
        window.location.href = 'perfil.php';
    });

    // Menu dos 3 pontos com a opção "Sair"
    if (dots) {
        dots.style.cursor = 'pointer';

        const menu = document.createElement('div');
        menu.className = 'menu-perfil-footer';
        menu.innerHTML = `
            <button type="button" class="menu-perfil-sair">
                <i class="bi bi-box-arrow-right"></i> Sair
            </button>
        `;
        footer.appendChild(menu);

        dots.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('show');
        });

        menu.addEventListener('click', (e) => e.stopPropagation());

        // Fecha o menu ao clicar fora
        document.addEventListener('click', () => menu.classList.remove('show'));

        menu.querySelector('.menu-perfil-sair').addEventListener('click', () => {
            const sair = () => { window.location.href = '../php/phplogin/logout.php'; };

            if (window.Swal) {
                Swal.fire({
                    title: 'Deseja sair?',
                    text: 'Você precisará fazer login novamente.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#e63946',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, sair',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) sair();
                });
            } else if (confirm('Deseja sair da sua conta?')) {
                sair();
            }
        });
    }
});