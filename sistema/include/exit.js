function setupLogout() {
    let logoutButton = document.getElementById('logout');
    if (logoutButton) {
        logoutButton.addEventListener('click', function() {
            // Remove os dados do usuário do localStorage
            localStorage.removeItem('user');
            // Redireciona para a página de login
            window.location.href = 'https://www.inovaretextil.com/sistema/login/';
        });
    }
}
