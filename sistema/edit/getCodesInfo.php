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

    // Recupera os códigos enviados
    $codes = $_POST['codes'] ?? [];
    if (empty($codes) || !is_array($codes)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid or missing codes.'
        ]);
        exit;
    }

    // Monta placeholders para a consulta (IN clause)
    $placeholders = implode(',', array_fill(0, count($codes), '?'));

    // Consulta para buscar os dados correspondentes
    $query = "SELECT id, codigo, desenho FROM Infos WHERE codigo IN ($placeholders)";
    $stmt = $conexao->prepare($query);
    $stmt->execute($codes);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'data' => $result
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No data found for the provided codes.'
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
