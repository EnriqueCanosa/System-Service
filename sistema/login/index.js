$(document).ready(function() {
    $('.show-password').click(function() {
        var $passwordInput = $('#pass');
        var $icon = $(this).find('i');
        
        if ($passwordInput.attr('type') === 'password') {
            $passwordInput.attr('type', 'text');
            $icon.removeClass('fa-eye').addClass('fa-eye-slash'); // Muda para o ícone de olho fechado
            $(this).text(' Hide Password').prepend($icon); // Atualiza o texto
        } else {
            $passwordInput.attr('type', 'password');
            $icon.removeClass('fa-eye-slash').addClass('fa-eye'); // Muda para o ícone de olho aberto
            $(this).text(' Show Password').prepend($icon); // Atualiza o texto
        }
    });

    $('.btnLogin').click(function(e) {
        e.preventDefault();
        
        let ident = $('#ident').val();
        let pass = $('#pass').val();
        
        if(ident === "" || pass === "") {
            $('.warning').text('Please enter your email, username or password before entering.');
            return;
        }

        // Requisição Ajax para o PHP
        $.ajax({
            url: 'log.php',
            type: 'POST',
            dataType: 'json',
            data: { 
                ident: ident, 
                pass: pass 
            },
            success: function(response) {
                if (response.status === 'success') {
                    let user = response.user;
                    localStorage.setItem('user', JSON.stringify(user));
                    window.location.href = 'panel.html';
                } else {
                    $('.warning').text(response.message);
                }
            },
            error: function() {
                $('.warning').text('Server error: please check your connection and try again');
            }
        });
    });
});
