$(document).ready(function() {

    let actualType = 1;

    const user = getUserData();

    document.getElementById('welcome').textContent = `${user.nome}`;
    document.getElementById('access').textContent = `Acess: ${user.tipo}`;

    // ------------ NEW SERVICE ------------
    $("#newServiceButton").click(function(){
        $('#newServiceModal').fadeIn();
    });
    $("#closeButtonNew").click(function(e){
        $('#newServiceModal').fadeOut();
    });
    $('#submitButton').on('click', function () {
        const user = getUserData();
        const costumer = $('#costumer').val().trim();
        const representative = $('#representative').val().trim();
        const infos = $('#infos').val().trim();
        
        const buttonText = $('#buttonText');
        const submitButton = $('#submitButton');
    
        if (!user.id) {
            window.location.href = "../login/index.html";
            return;
        }
        
        if (!representative || !infos) {
            $('.msg').text('Please fill in all required fields.')
            $('.msg').fadeIn();
            return;
        }

        buttonText.html('Processing <i class="fa-solid fa-spinner fa-spin-pulse"></i>');
        submitButton.prop('disabled', true);
    
        $.ajax({
            url: 'newService.php',
            type: 'POST',
            data: { 
                userId: user.id,
                costumer: costumer,
                representative: representative,
                infos: infos,
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const serviceData = response.serviceData;
                    buttonText.html('Success <i class="fa-regular fa-calendar-check"></i>');
                    submitButton.prop('disabled', false);
    
                    sessionStorage.setItem('serviceData', JSON.stringify(serviceData));
                    console.log('serviceData:', JSON.parse(sessionStorage.getItem('serviceData')));
    
                    // Fecha o modal e reseta o formulário
                    setTimeout(() => {
                        $('#newServiceModal').fadeOut();
                        $('#newServiceForm')[0].reset();
                        buttonText.html("Create Service");
                        submitButton.prop('disabled', false);
                        window.location.href = "../consult";
                    }, 1000);

                } else {
                    console.error('Response Error:', response.message);
                    $('.msg').text('An unknown error occurred: ' + response.message)
                    $('.msg').fadeIn();
                    buttonText.html('Error <i class="fa-regular fa-calendar-xmark"></i>');
                    submitButton.prop('disabled', false);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                $('.msg').text('An error occurred while communicating with the server: ' + error)
                $('.msg').fadeIn();
                buttonText.html('Error <i class="fa-regular fa-calendar-xmark"></i>');
                submitButton.prop('disabled', false);
            }
        });
    });

    // ------------ MY SERVICE ------------

    $("#closeButtonMy").click(function(e){
        $('#myServiceModal').fadeOut();
    });

    $("#MyServiceButton").click(function () {
        $('#myServiceModal').fadeIn();
        const user = getUserData();
    
        $.ajax({
            url: 'myService.php',
            type: 'POST',
            data: { 
                userId: user.id,
            },
            dataType: 'json',
            success: function (response) {
                // Salvando os dados no session Storage
                sessionStorage.setItem('myService', JSON.stringify(response));
                // Lendo os dados do session Storage para garantir que estão salvos corretamente
                console.log('myService:', JSON.parse(sessionStorage.getItem('myService')));

                const serviceList = $('.myServiceList');
                serviceList.empty();
                if (response.status === 'success') {
                    response.services.forEach(function (service) {
                        
                        const serviceCard = `
                        <div id="serviceCard" type=${service.id} class="serviceCard">
                            <div class="serviceHeader">
                                <h2 class="costumer">Costumer: ${service.costumer}</h2>
                                <span class="date">${service.data}</span>
                            </div>
                            <div class="representative">
                                Representante: ${service.representative}
                            </div>
                            <ul class="codes">
                                ${service.codes.split(',').map(code => `<li>${code.trim()}</li>`).join('')}
                            </ul>
                        </div>`;
                    serviceList.append(serviceCard);
                    });
                } else if (response.status === 'not_found') {
                    $('.msg').text('No services found.').fadeIn();
                } 
            },
            error: function (xhr, status, error) {
                $('.msg').text('Error: ' + error).fadeIn();
            }
        });
    });

    // ------------ INFO CARD MY SERVICE ------------

    $("#closeButtonInfo").click(function(e){
        $('#myServiceModal').fadeIn();
        $('#infoServiceModal').fadeOut();
    });

    $(document).on('click', '.serviceCard', function () {

        const type = $(this).attr('type');
        const storedData = JSON.parse(sessionStorage.getItem('myService'));
        const serviceData = storedData.services.find(service => service.id == type);
        const modal = $('#infoServiceModal');
        actualType = type;
        
        modal.find('.costumer').text(`Costumer: ${serviceData.costumer}`);
        modal.find('.date').text(serviceData.data);
        modal.find('.representative').text(`Representante: ${serviceData.representative}`);
        
        // Preenche os códigos como uma lista de <li>
        const codesList = modal.find('.codesInfo');
        codesList.empty(); // Limpa os códigos anteriores
        serviceData.codes.split(',').forEach(code => {
            if (code.trim()) {
                codesList.append(`<li>${code.trim()}</li>`);
            }
        });

        // Exibe o modal "infoServiceModal" e oculta o modal anterior
        $('#myServiceModal').fadeOut();
        modal.fadeIn();

    });
    
    $(document).on('click', '#infoButtonEdit', function () {

        const serviceType = actualType; 
        console.log(serviceType);

        $.ajax({
            url: 'getService.php',
            type: 'POST',
            data: { 
                serviceId: serviceType,
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    const serviceData = response.serviceData;
    
                    sessionStorage.setItem('serviceData', JSON.stringify(serviceData));
                    console.log('serviceData:', JSON.parse(sessionStorage.getItem('serviceData')));
    
                    // Redireciona para a página de edição
                    window.location.href = "../consult";
                } else {
                    console.error('Erro na resposta:', response.message);
                    alert('Erro: ' + response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Erro ao buscar serviço:', error);
                alert('Erro ao buscar serviço.');
            }
        });
    });
    

});


