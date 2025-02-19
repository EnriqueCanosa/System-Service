$(document).ready(function() {

    // confere se esta num service mode
    serviceData = sessionStorage.getItem('serviceData');
    console.log('serviceData:', JSON.parse(sessionStorage.getItem('serviceData')));

    if(serviceData){
        serviceData = JSON.parse(serviceData);
        $('#menu-service').show();
        $('#serviceCostumer').text(serviceData.costumer);
        $('#serviceRep').text(serviceData.representative);
        $('#serviceData').text(serviceData.data);
    }


    let userData = JSON.parse(localStorage.getItem('user'));

    if(userData.funcao == 0 || userData.funcao == 2 || userData.funcao == 3){
        $('#ident').show();
        $('#preco').show();
        $('#date3').show();
    }else{
        $('#ident').hide();
        $('#preco').hide();
        $('#date3').hide();
    }

    $('#view').click(function(){
        $('.more').removeClass('esconde');
        $('.moreButton').hide();
    });

    $('#searchButton').click(function() {
        let isFilled = false;
        $('#searchForm input, #searchForm select').each(function() {
            if ($(this).val().trim() !== '') {
                isFilled = true;
                return false; // Sai do loop assim que encontrar um campo preenchido
            }
        });
    
        if (!isFilled) {
            alert('Please fill in at least one input before continuing.');
        } else {
            let formData = $('#searchForm').serialize();
            $.ajax({
                url: 'busca.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    sessionStorage.setItem('searchResults', JSON.stringify(response));
                    window.location.href = 'results.html';
                },
                error: function() {
                    alert('An error occurred while processing the request. Please try again.');
                }
            });
        }
    });
    
    let inputIdent = document.querySelector('input[id="ident"]')

    inputIdent.addEventListener('input', function() {
    const inputVal = inputIdent.value;
    
        if (inputVal.startsWith('http')) {
            const parts = inputVal.split('=');
            const lastElement = parts[parts.length - 1];
            this.value = lastElement;
        }
    });

    $(document).on('click', '#finishServiceButton', function () {
        $('#menu-service').hide();
        sessionStorage.removeItem('serviceData');
        alert('Service mode finalized.');
    });
})
