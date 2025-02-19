function getUserData() {
    let user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

function confereLogin() {
    let user = getUserData();
    if (!user) {
        // Se o usuário não estiver logado, redireciona para a página de login
        window.location.href = 'https://www.inovaretextil.com/sistema/login/';
        console.log('user not logged')
    }else{
        console.log('user logged')
        console.log(user)
    }
}

confereLogin();