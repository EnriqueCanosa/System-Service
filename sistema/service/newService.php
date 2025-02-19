<?php
include_once '../include/conexao.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');


try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Redireciona para a página inicial se não for POST
        header('Location: index.html');
        exit;
    }

    $id_user = $_POST['userId'] ?? '';
    $costumer = $_POST['costumer'] ?? '';
    $representative = $_POST['representative'] ?? '';
    $infos = $_POST['infos'] ?? '';
    $data = date('Y-m-d');

    // Valida os campos obrigatórios
    if (empty($costumer) || empty($representative)) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Invalid: inputs cannot be empty.'
        ]);
        exit;
    }

    try {
        // Prepara a consulta de inserção
        $query = "INSERT INTO services ( id_user, costumer, representative, infos, data) 
                                VALUES (:id_user,:costumer,:representative,:infos,:data)";
        $stmt = $conexao->prepare($query);

        // Vincula os parâmetros
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindParam(':costumer', $costumer, PDO::PARAM_STR);
        $stmt->bindParam(':representative', $representative, PDO::PARAM_STR); 
        $stmt->bindParam(':infos', $infos, PDO::PARAM_STR);
        $stmt->bindParam(':data', $data, PDO::PARAM_STR);

        $stmt->execute();
        $lastId = $conexao->lastInsertId();

        echo json_encode([
            'status' => 'success',
            'message' => 'Service created successfully.',
            'serviceData' => [
                'id' => $lastId,
                'id_user' => $id_user,
                'costumer' => $costumer,
                'representative' => $representative,
                'infos' => $infos,
                'codes' => '',
                'data' => $data
                ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error on service creation: ' . $e->getMessage()
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Unexpected error: ' . $e->getMessage()
    ]);
}
?>
