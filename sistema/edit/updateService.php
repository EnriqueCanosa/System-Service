<?php
include_once '../include/conexao.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid request method. Only POST is allowed.'
        ]);
        exit;
    }

    // Recupera os dados enviados
    $serviceId = $_POST['serviceId'] ?? '';
    $codes = $_POST['codes'] ?? '';

    // Valida os campos obrigatórios
    if (empty($serviceId) || empty($codes)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields.'
        ]);
        exit;
    }

    // Atualiza os códigos na tabela service
    $query = "UPDATE services SET codes = :codes WHERE id = :serviceId";
    $stmt = $conexao->prepare($query);

    // Vincula os parâmetros
    $stmt->bindParam(':codes', $codes, PDO::PARAM_STR);
    $stmt->bindParam(':serviceId', $serviceId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Service updated successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update service.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unexpected error: ' . $e->getMessage()
    ]);
}
?>
