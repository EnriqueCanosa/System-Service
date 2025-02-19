

idAtualOnePage = 0;
codeAtualOnePage = 0;
dsnAtualOnePage = 0;

function getStoredIds() {
    return sessionStorage.getItem('storedIds') ? JSON.parse(sessionStorage.getItem('storedIds')) : [];
}

function updateServiceCodes() {
    let storedIds = getStoredIds(); // Recupera os IDs armazenados
    let serviceData = sessionStorage.getItem('serviceData');

    if (serviceData) {
        serviceData = JSON.parse(serviceData);

        // Atualiza os codes no serviceData com os códigos de storedIds
        serviceData.codes = storedIds.map(item => item.codigo); // Extrai os códigos

        // Salva o serviceData atualizado no sessionStorage
        sessionStorage.setItem('serviceData', JSON.stringify(serviceData));

        // Atualiza o serviço no backend
        $.ajax({
            url: 'updateService.php',
            type: 'POST',
            data: {
                serviceId: serviceData.id,
                codes: serviceData.codes.join(',') // Converte os códigos para string
            },
            success: function (response) {
                if (response.status === 'success') {
                    console.log('Service atualizado com sucesso:', response.message);
                } else {
                    console.error('Erro ao atualizar service:', response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Erro ao atualizar service:', error);
            }
        });
    }
}


function saveId(id, codigo, dsn) {
    let storedIds = getStoredIds();

    // Verifica se o item já existe no storedIds
    let exists = storedIds.some(item => item.id === id);

    if (!exists) {
        // Adiciona o novo item à lista
        storedIds.push({ id: id, codigo: codigo, dsn: dsn });
        sessionStorage.setItem('storedIds', JSON.stringify(storedIds));

        // Atualiza os codes do serviceData
        updateServiceCodes();
    }

    // Atualiza a interface
    moveIconFX();
    updateIconNumber(storedIds.length);
}

function moveIconFX(){
    $(".codesIconArea").css("right", "20px");
}

function updateIconNumber(count) {
    $(".codesIconNumber").text(count);
}

$(document).ready(function() {
    // Verifica se está no modo service
    let serviceData = sessionStorage.getItem('serviceData');
    //console.log('serviceData:', JSON.parse(serviceData));
    if (serviceData) {
        serviceData = JSON.parse(serviceData);

        // Exibe as informações do serviço no menu
        $('#menu-service').show();
        $('#serviceCostumer').text(serviceData.costumer);
        $('#serviceRep').text(serviceData.representative);
        $('#serviceData').text(serviceData.data);

        // Recupera os códigos do serviceData
        if (serviceData.codes && serviceData.codes.trim() !== '') {
            const codesArray = serviceData.codes.split(',').map(code => code.trim());

            // Faz AJAX para buscar informações dos códigos na tabela Infos
            $.ajax({
                url: 'getCodesInfo.php',
                type: 'POST',
                data: { codes: codesArray }, // Envia os códigos como array
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        // Itera pelos resultados e salva cada item usando saveId
                        response.data.forEach(item => {
                            saveId(item.id, item.codigo, item.dsn); // Salva no sistema
                        });
                    } else {
                        console.error('Erro na resposta:', response.message);
                        //alert('Erro ao buscar informações dos códigos: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro ao buscar informações dos códigos:', error);
                    //alert('Erro ao buscar informações dos códigos.');
                }
            });
        }
    }

    let storedIds = getStoredIds();
    if (storedIds.length > 0) {
        moveIconFX();
        updateIconNumber(storedIds.length);
    }

    $(document).on('click', '.noResultsBackButton', function() {
        window.location.href = 'index.php';
    });

    $(document).on('click', '.resultAdd', function() {
        let itemElement = $(this).closest('.resultItem'); // Acha o card pai
        let itemId = itemElement.attr('id'); // Captura o ID do card
        let itemCodigo = itemElement.find('.resultHeader h3').text().replace('Code: ', ''); // Captura o código
        let itemDsn = itemElement.find('.resultBody p:nth-child(2)').text().replace('DSN: ', ''); // Captura o DSN
        saveId(itemId, itemCodigo, itemDsn);
    });

    $("#onePageClose").click(function(){
        $('#onePageModal').fadeOut();
    });

    $("#onePageAdd").click(function(){
        saveId(idAtualOnePage, codeAtualOnePage, dsnAtualOnePage);
    });

    $("#onePageEdit").click(function(){
        alert('editing field in construction');
    });
    
    $("#codeListClose").click(function(){
        $('#codeListModal').fadeOut();
        $('.codesIcon').fadeIn()
    });

    $('.codeListMainMenuButton').click(function() {
        $(this).next('.codeListDropdownContent').slideToggle(300);
    });



    $(document).on('click', '.codeListRemove', function() {
        let itemId = $(this).closest('.codeListItem').attr('id');
        let storedIds = getStoredIds();
        storedIds = storedIds.filter(function(item) {
            return item.id != itemId;
        });
        sessionStorage.setItem('storedIds', JSON.stringify(storedIds));
        $(this).closest('.codeListItem').remove();
    
        updateIconNumber(storedIds.length);
    
        updateServiceCodes();
    });
    

    $(document).on('click', '#codeListRemoveAll', function() {
        let storedIds = getStoredIds();
        storedIds = [];
        sessionStorage.setItem('storedIds', JSON.stringify(storedIds));
        $('.codeListItem').remove();
        updateIconNumber(0);
    });
    

    let searchResults = sessionStorage.getItem('searchResults');
    //console.log(searchResults);
    if (searchResults) {
        let results = JSON.parse(searchResults);

        if (results.length > 0) {
            // Cria o HTML para cada resultado
            results.forEach(function(item) {
                let resultHTML = `
                    <div class="resultItem" id="${item.id}">
                        <div class="resultHeader">
                            <h3>Code: ${item.codigo}</h3>
                        </div>
                        <div class="resultBody">
                            <p><strong>Ident:</strong> ${item.ident}</p>
                            <p><strong>DSN:</strong> ${item.desenho}</p>
                            <p><strong>Supplier:</strong> ${item.fornecedor}</p>
                            <p><strong>Comp:</strong> ${item.composicao}</p>
                            <img class="img" src="${item.img_ft}" alt="No image">
                        </div>
                        <div class="resultButtonsArea">
                            <div class="resultAdd resultButton"><i class="fa fa-plus"></i></div>
                            <!--<div class="resultEdit resultButton"><i class="fa fa-pencil"></i></div>-->
                        </div>
                    </div>
                `;
                $('#resultsContainer').append(resultHTML);
            });
        } else {
            console.log('resultados da pesquisa retornaram zero correspondencias')
            $('#resultsContainer').html('<div class="noResultsBox">No results found.<div class="noResultsBackButton">Back<i class="fa-solid fa-angles-left"></i></div></div>');
        }
    } else {
        console.error('resultados da pesquisa não encontrados')
        $('#resultsContainer').html('<div class="noResultsBox">No results found.<div class="noResultsBackButton">Back<i class="fa-solid fa-angles-left"></i></div></div>');
    }

    let allSelected = false;

    $('#selectAllCodes').click(function() {
        let searchResults = sessionStorage.getItem('searchResults');
        
        if (searchResults) {
            let results = JSON.parse(searchResults);
            let storedIds = getStoredIds();

            if (!allSelected) {
                results.forEach(function(item) {
                    let exists = storedIds.some(storedItem => storedItem.id === item.id);
                    if (!exists) {
                        storedIds.push({ id: item.id, codigo: item.codigo, dsn: item.desenho });
                    }
                });

                sessionStorage.setItem('storedIds', JSON.stringify(storedIds));
                moveIconFX();
                updateIconNumber(storedIds.length); 
                $(this).html('REMOVE ALL CODES <i class="fa-solid fa-delete-left"></i>');
                allSelected = true;
            } else {
                storedIds = storedIds.filter(function(storedItem) {
                    return !results.some(resultItem => resultItem.id === storedItem.id);
                });

                sessionStorage.setItem('storedIds', JSON.stringify(storedIds));
                updateIconNumber(storedIds.length);

                $(this).html('ADD ALL CODES<i class="fa-solid fa-square-plus"></i>');
                allSelected = false;
            }
        }
    });


    $(".codesIcon").click(function(){
        let storedIds = getStoredIds();
        //console.log(storedIds);
        if (storedIds){
            $('.codeListContainer').find('.codeListItem').remove();
            let codeListHTML = '';
            storedIds.forEach(function(item) {
                codeListHTML += `
                    <div class="codeListItem" id='${item.id}'>
                        <p><strong>Code: </strong> ${item.codigo} <br><strong>DSN: </strong> ${item.dsn}</p>
                        <span class="codeListRemove">remove <i class="fa-solid fa-trash-can"></i></span>
                    </div>
                `;
            });
    
            $('.codeListContainer').append(codeListHTML);
            $('#codeListModal').fadeIn();
            $('.codesIcon').fadeOut()
        }
    });

    $(".resultBody").click(function(){
        let itemId = $(this).closest('.resultItem').attr('id'); 
        let searchResults = sessionStorage.getItem('searchResults');

        if (searchResults) {
            let results = JSON.parse(searchResults);
    
            let selectedItem = results.find(item => item.id == itemId);
            if (selectedItem) {
                idAtualOnePage = selectedItem.id;
                codeAtualOnePage = selectedItem.codigo;
                dsnAtualOnePage = selectedItem.desenho;
                let detailsHTML = `
                        <a target="_blank" style="color: white;" href="../ficha/?codigo=${selectedItem.ident}">
                            <h3>Code: ${selectedItem.codigo}</h3>
                        </a>
                        <p><strong>Ident:</strong> ${selectedItem.ident}</p>
                        <p><strong>Fornecedor:</strong> ${selectedItem.fornecedor}</p>
                        <p><strong>Composição:</strong> ${selectedItem.composicao}</p>
                        <p><strong>Gramatura:</strong> ${selectedItem.gramatura}</p>
                        <p><strong>Largura:</strong> ${selectedItem.largura}</p>
                        <p><strong>Desenho:</strong> ${selectedItem.desenho}</p>
                        <p><strong>Local:</strong> ${selectedItem.local}</p>
                        <p><strong>Família:</strong> ${selectedItem.familia}</p>
                        <p><strong>Preço:</strong> ${selectedItem.preco}</p>
                        <p><strong>Data Preço:</strong> ${selectedItem.data_price}</p>
                `;
                $("#onePageImg").attr("src", selectedItem.img_ft);

                $('#onePageModal').fadeIn();
                $('.onePageInfoContainer').html(detailsHTML);
            }
        }
    });

    $(document).on('click', '#finishServiceButton', function () {
        $('#menu-service').hide();
        sessionStorage.removeItem('serviceData');
        alert('Service mode finalized.');
    });
});
