<?php
require_once __DIR__ . '/../../composer/vendor/autoload.php'; // Inclui o autoloader do Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

session_start();
header('Access-Control-Allow-Origin: *');
include_once '../../include/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['selectedIds']) && !empty($data['selectedIds'])) {
        $ids = $data['selectedIds'];

        try {
            // Cria uma nova planilha
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Define o cabeçalho da planilha
            $sheet->setCellValue('A1', 'IDENT')
                  ->setCellValue('B1', 'ART NO.')
                  ->setCellValue('C1', 'SUPPLIER')
                  ->setCellValue('D1', 'COMP')
                  ->setCellValue('E1', 'WEIGHT')
                  ->setCellValue('F1', 'WIDTH')
                  ->setCellValue('G1', 'DSN NO.')
                  ->setCellValue('H1', 'NCM')
                  ->setCellValue('I1', 'COLOR FATNESS')
                  ->setCellValue('J1', 'QNTY PER COLOR')
                  ->setCellValue('K1', 'SHRIKAGE')
                  ->setCellValue('L1', 'PRICE REFERENCE')
                  ->setCellValue('M1', 'PRICE REFERENCE DATE')
                  ->setCellValue('N1', 'PRICE FABRIC');

            // Preenche os dados da planilha
            $rowIndex = 2;
            foreach ($ids as $id) {
                $query = "SELECT 
                            ident, 
                            codigo, 
                            fornecedor, 
                            composicao, 
                            gramatura, 
                            largura, 
                            desenho, 
                            ncm, 
                            color_fatness, 
                            qnty_per_color, 
                            shrikage, 
                            preco, 
                            data_price, 
                            price_fabric 
                        FROM Infos 
                        WHERE id = :id";
                $stmt = $conexao->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $sheet->setCellValue("A$rowIndex", $row['ident'])
                          ->setCellValue("B$rowIndex", $row['codigo'])
                          ->setCellValue("C$rowIndex", $row['fornecedor'])
                          ->setCellValue("D$rowIndex", $row['composicao'])
                          ->setCellValue("E$rowIndex", $row['gramatura'])
                          ->setCellValue("F$rowIndex", $row['largura'])
                          ->setCellValue("G$rowIndex", $row['desenho'])
                          ->setCellValue("H$rowIndex", $row['ncm'])
                          ->setCellValue("I$rowIndex", $row['color_fatness'])
                          ->setCellValue("J$rowIndex", $row['qnty_per_color'])
                          ->setCellValue("K$rowIndex", $row['shrikage'])
                          ->setCellValue("L$rowIndex", $row['preco'])
                          ->setCellValue("M$rowIndex", $row['data_price'])
                          ->setCellValue("N$rowIndex", $row['price_fabric']);
                    $rowIndex++;
                }
            }

            // Define o cabeçalho HTTP para forçar o download do arquivo Excel
            $fileName = "info_" . date('Ymd_His') . ".xlsx";
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$fileName\"");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            ob_clean(); // Limpa o buffer de saída
            $writer->save('php://output');
            exit;

        } catch (Exception $e) {
            // Tratar erros ao gerar o Excel
            http_response_code(500);
            echo 'Erro ao gerar o Excel: ' . $e->getMessage();
            exit;
        }
    } else {
        http_response_code(400);
        echo "Erro: Nenhum ID fornecido.";
        exit;
    }
} else {
    http_response_code(405);
    echo "Erro: Método de requisição inválido.";
    exit;
}
?>
