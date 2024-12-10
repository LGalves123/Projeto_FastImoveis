<?php

session_start();

if (!isset($_SESSION["isAdmin"]) || !$_SESSION["isAdmin"]) {
    die("Acesso negado.");
}

// Database connection parameters
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";

// Retrieve the ID of the property request from the request
$request_id = $_POST["request_id"] ?? "";

try {
    // Connect to the database
    $conn = new PDO($dbUrl, $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter dados da solicitação
    $sqlSelect = "SELECT * FROM solicitacoes_imoveis WHERE id = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    $stmtSelect->execute([$request_id]);
    $propertyRequest = $stmtSelect->fetch(PDO::FETCH_ASSOC);

    if ($propertyRequest) {
        // Prepare the SQL query to insert into "imoveis"
        $sqlInsert = "INSERT INTO imoveis (usuario_id, endereco, cidade, categoria, preco, nome_vendedor, telefone_vendedor, email_vendedor, status, foto, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        
        // Bind parameters for insertion
        $stmtInsert->bindParam(1, $propertyRequest['usuario_id']);
        $stmtInsert->bindParam(2, $propertyRequest['endereco']);
        $stmtInsert->bindParam(3, $propertyRequest['cidade']);
        $stmtInsert->bindParam(4, $propertyRequest['categoria']);
        $stmtInsert->bindParam(5, $propertyRequest['preco']);
        $stmtInsert->bindParam(6, $propertyRequest['nome_vendedor']);
        $stmtInsert->bindParam(7, $propertyRequest['telefone_vendedor']);
        $stmtInsert->bindParam(8, $propertyRequest['email_vendedor']);
        $stmtInsert->bindParam(8, $propertyRequest['status']);
        $stmtInsert->bindParam(10, $propertyRequest['foto']);
        $stmtInsert->bindParam(11, $propertyRequest['descricao']);
        
        // Execute the insertion
        $stmtInsert->execute();
        
        // Now delete the request from "solicitacao"
        $sqlDelete = "DELETE FROM solicitacao WHERE id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bindParam(1, $request_id);
        $stmtDelete->execute();

        header("Location: painel.php?message=Solicitação aprovada com sucesso.");
        exit();
    } else {
        echo "Solicitação não encontrada.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} finally {
    // Close resources
    $stmtSelect = null;
    $stmtInsert = null;
    $stmtDelete = null;
    $conn = null;
}
?>
