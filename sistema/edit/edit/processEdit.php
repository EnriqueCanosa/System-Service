<?php 
include_once '../../include/conexao.php';
//header('Content-Type: application/json; charset=utf-8');
// var_dump($_POST);
// exit;
$type = $_GET['type'] ?? '';

switch ($type) {
    case 'mass':
        try {
            // Verifica se os dados foram enviados via POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['massEdit'])) {
                $local = $_POST['massEdit']['local'] ?? null;
                $sublocal = $_POST['massEdit']['sublocal'] ?? null;
                $family = $_POST['massEdit']['family'] ?? null;
                echo $ids = explode(',', $_POST['massEdit']['ids'] ?? '');
    
                // Validar campos obrigatórios
                if (!$local || !$sublocal || !$family || empty($ids)) {
                    echo json_encode(['error' => 'Dados insuficientes.']);
                    exit;
                }
    
                $fullLocal = "$local - $sublocal";
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
                // Atualizar todos os IDs fornecidos
                $query = "UPDATE Infos SET local = ?, familia = ? WHERE id IN ($placeholders)";
                $stmt = $conexao->prepare($query);
    
                // Combinar os valores a serem atualizados com os IDs
                $params = array_merge([$fullLocal, $family], $ids);
    
                // Executar a consulta
                $stmt->execute($params);
    
                echo '<script> 
                        alert("Success");
                        window.location.href = "../results.html";
                    </script>';
            } else {
                header('Location: ../results.html'); // Redireciona caso a ação não seja válida
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }
        break;    

    case 'single':
        try {
            // Verifica se os dados foram enviados via POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codes'])) {
                foreach ($_POST['codes'] as $id => $data) {
                    $local = $data['local'] ?? null;
                    $sublocal = $data['sublocal'] ?? null;
                    $family = $data['family'] ?? null;

                    // Validar campos obrigatórios
                    if (!$local || !$sublocal || !$family || !$id) {
                        echo json_encode(['error' => "Dados insuficientes para o ID $id."]);
                        exit;
                    }

                    // Concatenar local e sublocal
                    $fullLocal = "$local - $sublocal";

                    // Atualizar o registro no banco
                    $query = "UPDATE Infos SET local = :local, familia = :familia WHERE id = :id";
                    $stmt = $conexao->prepare($query);
                    $stmt->execute([
                        ':local' => $fullLocal,
                        ':familia' => $family,
                        ':id' => $id
                    ]);
                }

                //echo json_encode(['success' => 'Success']);
                echo '<script> 
                        alert("Success");
                        window.location.href = "../results.html";
                    </script>';
                //header('Location: ../results.html'); 
            } else {
                header('Location: ../results.html'); // Redireciona caso a ação não seja válida
            }
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }
        break;

    default:
        header('Location: ../results.html'); // Redireciona caso a ação não seja válida
        exit;
}
?>
