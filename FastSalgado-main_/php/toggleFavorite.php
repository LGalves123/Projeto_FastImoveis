<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['idUsuario'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

if (!isset($_POST['imovelId'])) {
    error_log('ID do imóvel não fornecido'); // Adiciona log de erro
    echo json_encode(['success' => false, 'message' => 'ID do imóvel não fornecido']);
    exit();
}

$usuarioId = $_SESSION['idUsuario'];
$imovelId = $_POST['imovelId'];

error_log('ID do imóvel recebido: ' . $imovelId); // Adiciona log de depuração

try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao conectar ao banco de dados']);
    exit();
}

// Verificar se o imóvel já está favoritado
$query = "SELECT * FROM favoritos WHERE usuario_id = :usuarioId AND imovel_id = :imovelId";
$stmt = $conn->prepare($query);
$stmt->bindParam(':usuarioId', $usuarioId);
$stmt->bindParam(':imovelId', $imovelId);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Imóvel já está favoritado, então removemos dos favoritos
    $query = "DELETE FROM favoritos WHERE usuario_id = :usuarioId AND imovel_id = :imovelId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->bindParam(':imovelId', $imovelId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Imóvel removido dos favoritos']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao remover dos favoritos']);
    }
} else {
    // Imóvel não está favoritado, então adicionamos aos favoritos
    $query = "INSERT INTO favoritos (usuario_id, imovel_id) VALUES (:usuarioId, :imovelId)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuarioId', $usuarioId);
    $stmt->bindParam(':imovelId', $imovelId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Imóvel adicionado aos favoritos']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao adicionar aos favoritos']);
    }
}
?>
