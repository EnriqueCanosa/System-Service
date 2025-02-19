<?php
session_start();

// Verifica se o parâmetro "action" foi passado via GET
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    header('Location: ../results.html'); // Redireciona caso não exista "action"
    exit;
}

// Define o destino com base na ação
$formAction = '';
switch ($action) {
    case 'excel':
        $formAction = 'excel.php';
        $fileName = "infos_" . date('d_m') . ".xlsx";
        break;
    case 'ficha1':
        $formAction = 'ficha1.php';
        $fileName = "ficha_atendimento1" . date('d_m') . ".pdf";
        break;
    case 'ficha2':
        $formAction = 'ficha2.php';
        $fileName = "ficha_atendimento2" . date('d_m') . ".pdf";
        break;
    case 'ficha3':
        $formAction = 'ficha3.php';
        $fileName = "ficha_atendimento3" . date('d_m') . ".pdf";
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
    <title>Download Content</title>
    <link rel="icon" href="https://www.inovaretextil.com/sistema/layout/icon.png">
    <link rel="stylesheet" href="../css.css">
    <link rel="stylesheet" href="download.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- MENU SUPERIOR -->
    <div id="menu-superior"></div>
    <script src="../../include/confereLogin.js"> </script>
    <script src="../../include/addMenu.js"> </script>
    <!---->

    <a href="https://www.inovaretextil.com/sistema/login/panel.html">
        <div class='logo-container'>
            <div class="logo"></div>
            <span class="hover-text">Back to Home Panel <i class="fa-solid fa-house"></i></span>
        </div>
    </a>

    <form id="dynamicForm" action="<?php echo $formAction; ?>" method="post">
        <input type="hidden" name="selectedIds" id="selectedIds">
    </form>

    <div class="container">
        <div class="feedbackMessage" id="loadingMessage">
            <p id="loadingText">Generating files, please wait</p> 
            <img id="loadingGif" src="../../layout/loading.svg" alt="Carregando">
            <img id="loadingGood" src="../../layout/right.png" alt="Success" style="display: none;">
            <img id="loadingBad" src="../../layout/att.png" alt="Error" style="display: none;">
            <button onclick="window.location.href='../results.html'" id="backBtn" class="noResultsBackButton" style="display: none;">Back <i class="fa-solid fa-angles-left"></i></button>
        </div>
    </div>

    <script>
$(document).ready(function() {
    var ids = getStoredIds().map(item => item.id);
    console.log('Selected IDs:', JSON.stringify({ selectedIds: ids }));

    if (ids.length > 0) {
        $.ajax({
            url: $('#dynamicForm').attr('action'), // Determinado pelo PHP
            type: 'POST',
            contentType: 'application/json',
            timeout: 60000,
            data: JSON.stringify({ selectedIds: ids }),
            xhrFields: {
                responseType: 'blob' // Configura para tratar resposta binária
            },
            success: function(response) {
                if (response && response.size > 0) {
                    // Esconder gif de carregamento e mostrar ícone de sucesso
                    $('#loadingGif').hide();
                    $('#backBtn').show();
                    $('#loadingGood').show();
                    $('#loadingText').text('Files created with Success');

                    // Criar um link temporário para download do arquivo gerado
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(response);
                    link.download = '<?php echo $fileName; ?>'; // Define o nome do arquivo
                    link.click();
                    window.URL.revokeObjectURL(link.href); // Libera memória
                } else {
                    $('#loadingGif').hide();
                    $('#backBtn').show();
                    $('#loadingBad').show();
                    $('#loadingText').text('Error: Empty response from server.');
                }
            },
            error: function(xhr, status, error) {
                $('#loadingGif').hide();
                $('#backBtn').show();
                $('#loadingBad').show();

                if (status === "timeout") {
                    $('#loadingText').text('Error: The request timed out. Please try again.');
                } else {
                    $('#loadingText').text('Error on file generator: ' + error);
                }
            }
        });
    } else {
        window.location.href = "../results.html";
    }

    function getStoredIds() {
        return sessionStorage.getItem('storedIds') ? JSON.parse(sessionStorage.getItem('storedIds')) : [];
    }
});
    </script>
</body>
</html>
