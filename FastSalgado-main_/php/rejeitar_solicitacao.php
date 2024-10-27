<?php
// Ativa a exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Parâmetros de conexão com o banco de dados
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";

// Recupera o ID da solicitação a ser rejeitada
$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        // Conecta ao banco de dados
        $conn = new PDO($dbUrl, $dbUser, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Atualiza o status da solicitação para 'rejeitado'
        $sql = "UPDATE solicitacoes_imoveis SET status = 'rejeitado' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $id);
        
        // Executa a consulta
        if ($stmt->execute()) {
            echo "Solicitação rejeitada com sucesso!";
        } else {
            echo "Falha ao rejeitar a solicitação.";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    } finally {
        // Fecha os recursos
        $stmt = null;
        $conn = null;
    }
} else {
    echo "ID inválido.";
}
?>
