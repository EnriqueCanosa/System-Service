<?php
include_once '../include/conexao.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');


try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: index.html');
        exit;
    }

    $id_user = $_POST['userId'] ?? '';

    try {
        // Prepara a consulta de inserção
        $query = "SELECT * FROM services WHERE id_user = :id_user";

        $stmt = $conexao->prepare($query);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();

        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($services) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Services retrieved successfully.',
                'services' => $services
            ]);
        } else {
            echo json_encode([
                'status' => 'not_found',
                'message' => 'No services found for this user.',
                'services' => []
            ]);
        }
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
