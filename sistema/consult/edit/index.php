<?php
session_start();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    header('Location: ../results.html');
}

$formAction = '';
switch ($action) {
    case 'single':
        $formTitle = "Single Edit";
        break;
    case 'mass':
        $formTitle = "Mass Edit";
        break;
    default:
        header('Location: ../results.html'); // Redireciona caso a ação não seja válida
        exit;
}

?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Products</title>
    <link rel="icon" href="https://www.inovaretextil.com/sistema/layout/icon.png">
    <link rel="stylesheet" href="../css.css">
    <link rel="stylesheet" href="edit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- MENU SUPERIOR -->
    <div id="menu-superior"></div>
    <script src="../../include/confereLogin.js"></script>
    <script src="../../include/addMenu.js"></script>
    <!---->

    <a href="https://www.inovaretextil.com/sistema/login/panel.html">
        <div class='logo-container'>
            <div class="logo"></div>
            <span class="hover-text">Back to Home Panel <i class="fa-solid fa-house"></i></span>
        </div>
    </a>

    <h1><?php echo $formTitle; ?></h1>

    <form id="editForm" method="POST" action="processEdit.php?type=<?php echo $action?>">
        <div id="editContainer">
            <!-- formualrio adicionado dinamicamente -->
        </div>
        <div style="display:flex;">
            <input class="saveAllBt" type="submit" value="Save All">
        </div>
    </form>

    <script>
    $(document).ready(function() {
        // Função para obter os IDs e códigos armazenados no sessionStorage
        function getStoredIds() {
            return sessionStorage.getItem('storedIds') ? JSON.parse(sessionStorage.getItem('storedIds')) : [];
        }

        // Função para carregar opções dinamicamente via AJAX
        function loadSelectOptions(selectId, table) {
            $.ajax({
                url: 'getOptions.php',
                method: 'GET',
                data: { table: table },
                dataType: 'json',
                success: function(data) {
                    if (Array.isArray(data)) {
                        let select = $(`#${selectId}`);
                        select.empty();
                        select.append(new Option('Select an option', '')); 
                        data.forEach(function(option) {
                            select.append(new Option(option.name, option.name));
                        });
                    } else {
                        console.error('Data is not an array:', data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`Failed to load options for ${selectId}. Error: ${error}`);
                }
            });
        }

        var idsAndCodes = getStoredIds();
        var ids = getStoredIds().map(item => item.id);
        console.log('Selected IDs and Codes:', JSON.stringify(idsAndCodes));

        var action = '<?php echo $action; ?>';

        // Cria o formulário baseado na ação
        if (action === 'single') {
            idsAndCodes.forEach(function(item) {
                const id = item.id;
                const code = item.codigo;
                const dsn = item.dsn;

                $('#editContainer').append(`
                    <div class="form-group">
                        <h2>Code: ${code}</h2>
                        <h3>DSN: ${dsn} </h3>
                        
                        <div class="input-group">
                            <label for="local_${id}">Local:</label>
                            <select id="local_${id}" name="codes[${id}][local]" required class="form-select"></select>
                        </div>
                        
                        <div class="input-group">
                            <label for="sublocal_${id}">Sub Local:</label>
                            <select id="sublocal_${id}" name="codes[${id}][sublocal]" required class="form-select"></select>
                        </div>
                        
                        <div class="input-group">
                            <label for="family_${id}">Family:</label>
                            <select id="family_${id}" name="codes[${id}][family]" required class="form-select"></select>
                        </div>
                    </div>
                `);

                loadSelectOptions(`local_${id}`, 'locals');
                loadSelectOptions(`sublocal_${id}`, 'sub_locals');
                loadSelectOptions(`family_${id}`, 'family');
            });
        } else if (action === 'mass') {
            $('#editContainer').append(`
                <div class="form-group">
                    <h3>Changing information for ALL the selected codes</h3>
                    
                    <div class="input-group">
                        <label for="local_mass">Local:</label>
                        <select id="local_mass" name="massEdit[local]" required class="form-select"></select>
                    </div>
                    
                    <div class="input-group">
                        <label for="sublocal_mass">Sub Local:</label>
                        <select id="sublocal_mass" name="massEdit[sublocal]" required class="form-select"></select>
                    </div>
                    
                    <div class="input-group">
                        <label for="family_mass">Family:</label>
                        <select id="family_mass" name="massEdit[family]" required class="form-select"></select>
                    </div>

                    <input type="hidden" id="ids_mass" name="massEdit[ids]" value="${ids.join(',')}">

                </div>
            `);

            loadSelectOptions('local_mass', 'locals');
            loadSelectOptions('sublocal_mass', 'sub_locals');
            loadSelectOptions('family_mass', 'family');
        }
    });
    </script>


</body>
</html>
