<?php 
session_start();
header('Access-Control-Allow-Origin: *');
ob_start();

require '../composer/vendor/autoload.php';

include_once '../include/conexao.php';

date_default_timezone_set('America/Sao_Paulo');
$data_registrobr = date('Y-m-d');

$nome = $_POST['nameUser'];
$nome_empresa = $_POST['nameComp'];
$arquivo = $_FILES['arquivo'];

echo 'Data: ' . $data_registrobr . '<br>';
echo 'nome: ' . $nome . '<br>';
echo 'empresa: ' . $nome_empresa . '<br>';
echo '<pre>';
var_dump($arquivo);
echo '</pre>';

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o arquivo foi enviado sem erros
    if (isset($_FILES["arquivo"]) && $_FILES["arquivo"]["error"] == 0) {
        // Obtém o nome temporário do arquivo enviado
        $tempFile = $_FILES["arquivo"]["tmp_name"];

        // Cria um objeto PhpSpreadsheet a partir do arquivo Excel
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempFile);

        // Obtém a primeira planilha (índice 0)
        $sheet = $spreadsheet->getSheet(0);

        // Obtém as linhas da planilha
        $rows = $sheet->toArray();

        // Busca última data 
            $queryUltimaData = "SELECT data FROM contador WHERE id = 1";
            $resultado = $conexao->query($queryUltimaData);
            $stmt = $resultado->fetch(PDO::FETCH_ASSOC);
            $ultimaData = $stmt['data'];

        if($ultimaData === $data_registrobr){
            //recupera contador
            echo "datas batem!" . "<br>";
            $queryUltimoContador = "SELECT contador FROM contador WHERE id = 1";
            $resultado2 = $conexao->query($queryUltimoContador);
            $stmt2 = $resultado2->fetch(PDO::FETCH_ASSOC);
            $contador_inovare_counter = $stmt2['contador'];
        }else{
            //Caso seja datas diferente, deixa igual a 1
            $contador_inovare_counter = 1;
        }
        
        echo "ultimo contadorzada: " .  $contador_inovare_counter . "<br>";
        echo "ultima datazada: " . $ultimaData . "<br>";

        // tirar cabeçalho
        $primeira_linha = true;

        // Loop para inserir os dados no banco de dados
        foreach ($rows as $linha) {
            if ($primeira_linha) {
                $primeira_linha = false;
                continue;
            }

            // Verifica se o valor da primeira coluna está vazio
            $ident = (!empty($linha[0]) ? $linha[0] : '');

            // Se estiver vazio, gera um novo ident seguindo a lógica especificada
            if (empty($ident)) {
                $ident = 'A' . date('ymd') . sprintf('%03d', $contador_inovare_counter);
                // Incrementa o contador
                $contador_inovare_counter++;
            }

            echo $query_usuario =
            "INSERT INTO Infos 
            ( ident, codigo, fornecedor, composicao, gramatura, largura, desenho, ncm, color_fatness, qnty_per_color, shrikage, preco, data_price, price_fabric, data_registrobr, nome, nome_empresa, qr_link, img_qr, img_ft) 
            VALUES 
            (:ident,:codigo,:fornecedor,:composicao,:gramatura,:largura,:desenho,:ncm,:color_fatness,:qnty_per_color,:shrikage,:preco,:data_price,:price_fabric,:data_registrobr,:nome,:nome_empresa,:qr_link,:img_qr,:img_ft)
            ";

            $cad_usuario = $conexao->prepare($query_usuario);
            $cad_usuario->bindValue(':ident', $ident);
            $cad_usuario->bindValue(':codigo', ($linha[1] ?? ""));
            $cad_usuario->bindValue(':fornecedor', ($linha[2] ?? ""));
            $cad_usuario->bindValue(':composicao', ($linha[3] ?? ""));
            $cad_usuario->bindValue(':gramatura', ($linha[4] ?? ""));
            $cad_usuario->bindValue(':largura', ($linha[5] ?? ""));
            $cad_usuario->bindValue(':desenho', ($linha[6] ?? ""));
            $cad_usuario->bindValue(':ncm', ($linha[7] ?? ""));
            $cad_usuario->bindValue(':color_fatness', ($linha[8] ?? ""));
            $cad_usuario->bindValue(':qnty_per_color', ($linha[9] ?? ""));
            $cad_usuario->bindValue(':shrikage', ($linha[10] ?? ""));
            $cad_usuario->bindValue(':preco', ($linha[11] ?? ""));
            $cad_usuario->bindValue(':data_price', ($linha[12] ?? ""));
            $cad_usuario->bindValue(':price_fabric', ($linha[13] ?? ""));
            $cad_usuario->bindValue(':data_registrobr', $data_registrobr, PDO::PARAM_STR);
            $cad_usuario->bindValue(':nome', $nome, PDO::PARAM_STR);
            $cad_usuario->bindValue(':nome_empresa', $nome_empresa, PDO::PARAM_STR);
            $cad_usuario->bindValue(':qr_link', 'https://inovaretextil.com/sistema/ficha/?codigo=' . $ident, PDO::PARAM_STR);
            $cad_usuario->bindValue(':img_qr', 'https://inovaretextil.com/sistema/layout/imagens/qr/' . $ident . '.png', PDO::PARAM_STR);
            $cad_usuario->bindValue(':img_ft', 'https://inovaretextil.com/sistema/layout/imagens/tecido/' . $ident . '.jpg', PDO::PARAM_STR);
            $cad_usuario->execute();
        }

        // Atualiza a tabela contador
        $query_atualizar_contador = "UPDATE contador SET contador = :contador, data = :data WHERE id = 1";
        $cad_contador = $conexao->prepare($query_atualizar_contador);
        $cad_contador->bindValue(':data', $data_registrobr);
        $cad_contador->bindValue(':contador', $contador_inovare_counter);
        $cad_contador->execute();

        $_SESSION['message'] = "Success!";
        header("Location: index.php");
        //echo "Dados inseridos com sucesso!";
    } else {
        $_SESSION['message'] = "Upload file error, try again later";
        header("Location: index.php");
        //echo "Erro no upload do arquivo.";
    }
} else {
    header("Location: index.php");
}
?>
