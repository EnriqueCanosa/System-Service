<?php

include_once '../include/conexao.php';

// Função para pegar valor do POST ou retornar vazio
function getPostValue($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// Recebe os dados do POST
$ident = getPostValue('ident');
$date1 = getPostValue('date1');
$date2 = getPostValue('date2');
$codigo = getPostValue('codigo');
$preco = getPostValue('preco');
$date3 = getPostValue('date3');
$fornecedor = getPostValue('fornecedor');
$composicao = getPostValue('composicao');
$gramatura = getPostValue('gramatura');
$largura = getPostValue('largura');
$desenho = getPostValue('desenho');
$local = getPostValue('local');
$sublocal = getPostValue('sublocal');
$familia = getPostValue('familia');

// Combina local e sublocal se ambos estiverem presentes
$local_tot = '';
if ($local !== '' || $sublocal !== '') {
    if ($local !== '' && $sublocal !== '') {
        $local_tot = $local . ' - ' . $sublocal;
    } else if ($local !== '' && $sublocal == '') {
        $local_tot = $local;
    } else {
        $local_tot = $sublocal;
    }
}

// Monta a query base
$query = "SELECT * FROM Infos WHERE 1=1";
$params = [];

// Adiciona condições de acordo com os filtros recebidos
if ($ident !== '') {
    $query .= " AND ident LIKE :ident";
    $params[':ident'] = "%$ident%";
}

if ($date1 !== '' && $date2 !== '') {
    $query .= " AND data_registrobr BETWEEN :date1 AND :date2";
    $params[':date1'] = $date1;
    $params[':date2'] = $date2;
} elseif ($date1 !== '') {
    $query .= " AND data_registrobr >= :date1";
    $params[':date1'] = $date1;
} elseif ($date2 !== '') {
    $query .= " AND data_registrobr <= :date2";
    $params[':date2'] = $date2;
}

if ($codigo !== '') {
    $query .= " AND codigo LIKE :codigo";
    $params[':codigo'] = "%$codigo%";
}

if ($preco !== '') {
    $query .= " AND preco LIKE :preco";
    $params[':preco'] = "%$preco%";
}

if ($date3 !== '') {
    $query .= " AND data_preco = :date3";
    $params[':date3'] = $date3;
}

if ($fornecedor !== '') {
    $query .= " AND fornecedor LIKE :fornecedor";
    $params[':fornecedor'] = "%$fornecedor%";
}

if ($composicao !== '') {
    $query .= " AND composicao LIKE :composicao";
    $params[':composicao'] = "%$composicao%";
}

if ($gramatura !== '') {
    $query .= " AND gramatura LIKE :gramatura";
    $params[':gramatura'] = "%$gramatura%";
}

if ($largura !== '') {
    $query .= " AND largura LIKE :largura";
    $params[':largura'] = "%$largura%";
}

if ($desenho !== '') {
    $query .= " AND desenho LIKE :desenho";
    $params[':desenho'] = "%$desenho%";
}

if ($local_tot !== '') {
    $query .= " AND local_sublocal LIKE :local_tot";
    $params[':local_tot'] = "%$local_tot%";
}

if ($familia !== '') {
    $query .= " AND familia LIKE :familia";
    $params[':familia'] = "%$familia%";
}

// Prepara e executa a consulta
$stmt = $conexao->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retorna os resultados em formato JSON
header('Content-Type: application/json');
echo json_encode($results);
?>