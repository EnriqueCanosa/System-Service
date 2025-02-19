<?php

ini_set('session.cookie_domain', '.inovaretextil.com');
header('Access-Control-Allow-Origin: *');
include_once '../include/conexao.php';

if (empty($_POST['ident']) || empty($_POST['pass'])) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Please enter your email, username or password before entering.'
        ]);
    exit;
}

$ident = $_POST['ident'];
$pass = $_POST['pass'];

// Verifica o login
$query = "SELECT * FROM admins WHERE (user = :ident OR email = :ident) AND pass = :pass LIMIT 1";
$stmt = $conexao->prepare($query);
$stmt->bindValue(':ident', $ident, PDO::PARAM_STR);
$stmt->bindValue(':pass', $pass, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Retorna informações do usuário no formato JSON
    echo json_encode([
        'status' => 'success',
        'user' => [
            'id' => $user['id'],
            'nome' => $user['nome'],
            'funcao' => $user['funcao'],
            'tipo' => $user['tipo']
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Email, user or password invalid. Try again.'
    ]);
}
?>
