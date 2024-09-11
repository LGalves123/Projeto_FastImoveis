<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");

    // Check if the request contains a record ID
    $recordIdStr = $_POST["recordId"] ?? null;

    if ($recordIdStr !== null) {
        $recordId = intval($recordIdStr);

        // Perform the record update
        $updateQuery = "UPDATE imoveis SET endereco = ?, cidade = ?,categoria = ? WHERE id=?";
        $updateStatement = $conn->prepare($updateQuery);

        // Replace "endereco" and "categoria" with your actual column names
        $updateStatement->bindParam(1, $_POST["endereco"]);
        $updateStatement->bindParam(2, $_POST["cidade"]);
        $updateStatement->bindParam(3, $_POST["categoria"]);
        $updateStatement->bindParam(4, $recordId, PDO::PARAM_INT);

        $updateStatement->execute();

        $rowsUpdated = $updateStatement->rowCount();

        // Send a response back to the JavaScript
        if ($rowsUpdated > 0) {
            echo json_encode(["success" => true, "message" => "Record updated successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to update the record."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid record ID."]);
    }

    $conn = null;
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
