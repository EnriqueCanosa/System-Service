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
                    .invisible-block {
                        height: 180px;
                        visibility: hidden;
                    }
                    .info-block {
                        width: 90%;
                        height: 200px;
                        border-radius: 0 40px 40px 0;
                        background-color: #9d9d9d;
                        margin-bottom: 20px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 10px 15px;
                    }
                    .info-text {
                        display: flex;
                        font-size: 16px;
                        color: #000;
                        width: 300px;
                        height: 190px;
                        flex-direction: column;
                        justify-content: space-evenly;
                    }
                    .item{
                        margin: 2px 0
                    }
                    .info-box {
                        position:fixed;
                        border-radius: 40px;
                        width: 390px;
                        height: 190px;
                        background-color: #fff;
                        float: inline-end;
                        margin-top: -190px;
                        margin-left: 40%;
                    }
                    .bg {
                        background-image: url("https://www.inovaretextil.com/sistema/layout/bg_ficha3.jpg");
                        background-size: cover;
                        background-repeat: no-repeat;
                        width: 100%;
                        height: 100%;
                        position: relative;
                    }
                    .footerMsg {
                        position: fixed;
                        bottom: 30px;
                        right: 180px;
                        font-size: 16px;
                        text-align: right;
                    }
                    .footerQR {
                        position: fixed;
                        bottom: 30px;
                        right: 30px;
                        width: 120px;
                        height: 120px;
                    }
                </style>
            </head>
            <body class="bg">
                <p class="footerMsg">
                    Rua Ribeiro de Lima, 282 Conj. 1303 - Bom Retiro . São Paulo, SP . 01122.000 <br>
                    Tel.: +55 11 3313.3994 . @inovare_brasil . inovaretextil.com
                </p>
                <img class="footerQR" src="https://www.inovaretextil.com/sistema/layout/qr_ino.png">';

            $count = 0;
            $totalIds = count($ids);

            foreach ($ids as $index => $id) {
                // Adiciona o bloco invisível no início de cada página (1ª linha de cada grupo de 3)
                if ($count % 3 == 0) {
                    $html .= '<div class="invisible-block"></div>';
                }

                $query = "SELECT * FROM Infos WHERE id = :id";
                $stmt = $conexao->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $html .= '
                    <div class="info-block">
                        <div class="info-text">
                            <p class="item">Fornecedor: ' . htmlspecialchars($row['fornecedor']) . '</p>
                            <p class="item">Código: ' . htmlspecialchars($row['codigo']) . '</p>
                            <p class="item">Desenho: ' . htmlspecialchars($row['desenho']) . '</p>
                            <p class="item">Composição: ' . htmlspecialchars($row['composicao']) . '</p>
                            <p class="item">Largura: ' . htmlspecialchars($row['largura']) . '</p>
                            <p class="item">Gramatura: ' . htmlspecialchars($row['gramatura']) . '</p>
                            <p class="item">MCQ:</p>
                            <p class="item">Preço:</p>
                        </div>
                        <div class="info-box"></div>
                    </div>';

                    $count++;

                    // Adiciona quebra de página após 3 blocos, exceto no último bloco
                    if ($count % 3 == 0 && $index != $totalIds - 1) {
                        $html .= '<div style="page-break-after: always;"></div>';
                    }
                }
            }

            $html .= '
            </body>
            </html>';

            // Carrega o HTML no Dompdf
            $dompdf->loadHtml($html);

            // Define o papel como A4 e a orientação como vertical, sem margens
            $dompdf->setPaper('A4', 'portrait');

            // Renderiza o PDF
            $dompdf->render();

            // Envia o PDF para o navegador
            $dompdf->stream('ficha2.pdf', ["Attachment" => false]);
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
