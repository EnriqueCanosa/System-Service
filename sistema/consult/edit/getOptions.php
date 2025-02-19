<?php
include_once '../../include/conexao.php';

// Definir o cabeçalho para a resposta como JSON e definir UTF-8
header('Content-Type: application/json; charset=utf-8');

$table = $_GET['table'] ?? '';

switch ($table) {
    case 'locals':
    case 'sub_locals':
    case 'family':
        try {
            $stmt = $conexao->prepare("SELECT id, name FROM $table WHERE status = 1");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Erro ao buscar dados da tabela'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        break;
    default:
        echo json_encode(['error' => 'Tabela inválida'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        break;
}
?>
