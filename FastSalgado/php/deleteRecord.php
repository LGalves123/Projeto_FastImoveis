<?php
session_start();
try {
    $conn = new PDO("mysql:host=localhost;dbname=FastImoveis;charset=utf8mb4", "root", "");

    // Check if the request contains a record ID
    $recordIdStr = $_POST["recordId"] ?? null;
    if ($recordIdStr !== null) {
        $recordId = intval($recordIdStr);

        // Perform the record deletion
        $deleteQuery = "DELETE FROM imoveis WHERE id=?";
        $preparedStatement = $conn->prepare($deleteQuery);
        $preparedStatement->bindParam(1, $recordId, PDO::PARAM_INT);
        $preparedStatement->execute();

        $rowsDeleted = $preparedStatement->rowCount();

        // Send a response back to the JavaScript
        if ($rowsDeleted > 0) {
            echo json_encode(["success" => true, "message" => "Record deleted successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to delete the record."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid record ID."]);
    }

    $conn = null;
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>