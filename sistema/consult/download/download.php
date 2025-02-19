<?php
if (isset($_GET['file'])) {
    $fileName = $_GET['file'];
    $filePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $fileName;

    if (file_exists($filePath)) {
        // Define o Content-Type de acordo com o tipo de arquivo
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        switch ($fileExtension) {
            case 'xlsx':
                $contentType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;
            case 'pdf':
                $contentType = 'application/pdf';
                break;
            default:
                echo "Erro: Tipo de arquivo não suportado.";
                exit;
        }

        header('Content-Type: ' . $contentType);
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        readfile($filePath);
        unlink($filePath); // Exclui o arquivo após o download
        exit;
    } else {
        echo "Erro: Arquivo não encontrado.";
        exit;
    }
} else {
    echo "Erro: Parâmetro de arquivo ausente.";
    exit;
}
?>
