<?php
require_once __DIR__ . '/../../composer/vendor/autoload.php'; // Inclui o autoloader do Composer

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
header('Access-Control-Allow-Origin: *');
include_once '../../include/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['selectedIds']) && !empty($data['selectedIds'])) {
        $ids = $data['selectedIds'];

        try {
            // Configurações do Dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);

            $html = '
            <html>
            <head>
                <style>
                    @page {
                        margin: 0;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                        font-family: Monteserrat, sans-serif;
                        font-weight: bold;
                    }
                    .page {
                        width: 100%;
                        height: 100%;
                        page-break-after: always;
                        position: relative;
                    }
                    .bg {
                        width: 100%; 
                        height: 100%; 
                        background-image: url(\'https://www.inovaretextil.com/sistema/layout/bg_ficha.jpg\'); 
                        background-size: cover; 
                        background-repeat: no-repeat; 
                        position: absolute; 
                        top: 0; 
                        left: 0;
                    }
                    .infoBlock {
                        position: absolute; 
                        top: 35px; 
                        right: 25px; 
                        width: 280px; 
                        height: auto; 
                        display: flex; 
                        flex-direction: column; 
                        align-items: flex-start; 
                        justify-content: space-evenly; 
                        background-color: rgba(255, 255, 255, 1); 
                        padding: 15px; 
                        border-radius: 40px; 
                        border: 2px solid #9c9c9c;
                    }
                    .infoLine {
                        font-size: 15px; 
                        margin: 0;
                    }
                    .footerMsg {
                        position: absolute; 
                        bottom: 30px; 
                        right: 180px; 
                        font-size: 16px; 
                        text-align: right;
                    }
                    .footerQR {
                        position: absolute; 
                        bottom: 30px; 
                        right: 30px; 
                        width: 120px; 
                        height: 120px;
                    }
                </style>
            </head>
            <body>';

            // Para cada ID, gera uma nova página do PDF
            foreach ($ids as $id) {
                $query = "SELECT * FROM Infos WHERE id = :id";
                $stmt = $conexao->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $html .= '
                    <div class="page">
                        <div class="bg"></div>
                        <div class="infoBlock">
                            <p class="infoLine">Fornecedor: ' . htmlspecialchars($row['fornecedor']) . '</p>
                            <p class="infoLine">Código: ' . htmlspecialchars($row['codigo']) . '</p>
                            <p class="infoLine">Desenho: ' . htmlspecialchars($row['desenho']) . '</p>
                            <p class="infoLine">Composição: ' . htmlspecialchars($row['composicao']) . '</p>
                            <p class="infoLine">Largura: ' . htmlspecialchars($row['largura']) . '</p>
                            <p class="infoLine">Gramatura: ' . htmlspecialchars($row['gramatura']) . '</p>
                            <p class="infoLine">MCQ:</p>
                            <p class="infoLine">Preço:</p>
                        </div>

                        <p class="footerMsg">
                            Rua Ribeiro de Lima, 282 Conj. 1303 - Bom Retiro . São Paulo, SP . 01122.000 <br>
                            Tel.: +55 11 3313.3994 . @inovare_brasil . inovaretextil.com
                        </p>
                        <img class="footerQR" src="https://www.inovaretextil.com/sistema/layout/qr_ino.png">
                    </div>';
                }
            }

            $html .= '</body></html>';

            // Carrega o HTML no Dompdf
            $dompdf->loadHtml($html);

            // Define o papel como A4 e a orientação como vertical, sem margens
            $dompdf->setPaper('A4', 'portrait');

            // Renderiza o PDF
            $dompdf->render();

            // Define os cabeçalhos HTTP para forçar o download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="ficha1.pdf"');
            header('Cache-Control: public, must-revalidate, max-age=0');
            header('Pragma: public');
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

            echo $dompdf->output();
            exit;

        } catch (Exception $e) {
            // Tratar erros do Dompdf
            http_response_code(500);
            echo 'Error on PDF generating: ' . $e->getMessage();
            exit;
        }
    } else {
        http_response_code(400);
        echo "Error: No ID provided.";
        exit;
    }
} else {
    http_response_code(405);
    echo "Error: Invalid request method.";
    exit;
}
?>
