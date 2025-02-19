<?php
include_once '../include/conexao.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

try {
    // Verifica o método da requisição
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method. Only POST is allowed.'
        ]);
        exit;
    }

    // Recupera o ID do serviço enviado na requisição
    $serviceId = $_POST['serviceId'] ?? '';

    // Valida o ID do serviço
    if (empty($serviceId) || !is_numeric($serviceId)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid service ID provided.'
        ]);
        exit;
    }

    // Executa a consulta para buscar o serviço
    $query = "SELECT * FROM services WHERE id = :serviceId";
    $stmt = $conexao->prepare($query);

    // Vincula os parâmetros
    $stmt->bindParam(':serviceId', $serviceId, PDO::PARAM_INT);
    $stmt->execute();

    // Recupera o resultado da consulta
    $serviceDados = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o serviço foi encontrado
    if ($serviceDados) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Service retrieved successfully.',
            'serviceData' => [
                'id' => $serviceDados["id"],
                'id_user' => $serviceDados["id_user"],
                'costumer' => $serviceDados["costumer"],
                'representative' => $serviceDados["representative"],
                'infos' => $serviceDados["infos"],
                'codes' => $serviceDados["codes"],
                'data' => $serviceDados["data"]
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Service not found.'
        ]);
    }
} catch (PDOException $e) {
    // Trata erros de banco de dados
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Trata outros erros gerais
    echo json_encode([
        'status' => 'error',
        'message' => 'Unexpected error: ' . $e->getMessage()
    ]);
}
?>