<?php
// Database connection parameters
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";

// File upload parameters
$targetDir = "img/"; // Directory where uploaded files will be stored
$errorMessages = []; // Array to store error messages

try {
    // Connect to the database
    $conn = new PDO($dbUrl, $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve form data from request
    $endereco = $_POST["endereco"] ?? "";
    $cidade = $_POST["cidade"] ?? "";
    $categoria = $_POST["categoria"] ?? "";
    $preco = $_POST["preco"] ?? "";
    $nome_vendedor = $_POST["nome_vendedor"] ?? "";
    $telefone_vendedor = $_POST["telefone_vendedor"] ?? "";
    $email_vendedor = $_POST["email_vendedor"] ?? "";
    $descricao = $_POST["descricao"] ?? "";
    $status = $_POST['status'] ?? 'pendente'; // Default value
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;

    // Validate mandatory fields
    if (empty($endereco) || empty($cidade) || empty($categoria) || empty($preco) || empty($nome_vendedor) || empty($telefone_vendedor) || empty($email_vendedor)) {
        $errorMessages[] = "Por favor, preencha todos os campos obrigatórios.";
    }

    // Check if status is valid
    $validStatuses = ['pendente', 'aprovado', 'rejeitado'];
    if (!in_array($status, $validStatuses)) {
        $status = 'pendente'; // Default value if invalid
    }

    // File upload logic
    $targetFile = $targetDir . basename($_FILES["foto"]["name"]); // Path of the target file
    $uploadOk = 1;

    // Check if file already exists and rename if necessary
    if (file_exists($targetFile)) {
        $fileInfo = pathinfo($targetFile);
        $fileBaseName = $fileInfo['filename'];
        $fileExtension = $fileInfo['extension'];
        $counter = 1;

        while (file_exists($targetFile)) {
            $targetFile = $targetDir . $fileBaseName . "_" . $counter . "." . $fileExtension;
            $counter++;
        }
    }

    // Check file size
    if ($_FILES["foto"]["size"] > 5000000) { // Limit file size
        $errorMessages[] = "Desculpe, o arquivo é muito grande.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
    $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        $errorMessages[] = "Desculpe, apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
        $uploadOk = 0;
    }

    // Attempt to move the uploaded file
    if ($uploadOk == 1 && move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
        // Prepare SQL query to insert a new record
        $sql = "INSERT INTO solicitacoes_imoveis (endereco, cidade, categoria, preco, nome_vendedor, telefone_vendedor, email_vendedor, status, foto, descricao, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $endereco);
        $stmt->bindParam(2, $cidade);
        $stmt->bindParam(3, $categoria);
        $stmt->bindParam(4, $preco);
        $stmt->bindParam(5, $nome_vendedor);
        $stmt->bindParam(6, $telefone_vendedor);
        $stmt->bindParam(7, $email_vendedor);
        $stmt->bindParam(8, $status);
        $stmt->bindParam(9, $targetFile);
        $stmt->bindParam(10, $descricao);
        $stmt->bindParam(11, $latitude);  // Adicionando latitude
        $stmt->bindParam(12, $longitude); // Adicionando longitude

        // Execute the SQL query to insert the new record
        $stmt->execute();
        $rowsAffected = $stmt->rowCount();

        if ($rowsAffected > 0) {
            // Record was successfully added
            header("Location: painel.php");
            exit();
        } else {
            $errorMessages[] = "Erro ao adicionar o registro.";
        }
    } else {
        $errorMessages[] = "Desculpe, ocorreu um erro ao enviar o arquivo.";
    }
} catch (PDOException $e) {
    $errorMessages[] = "Error: " . $e->getMessage();
} finally {
    // Close resources
    if (isset($stmt)) {
        $stmt = null;
    }
    if (isset($conn)) {
        $conn = null;
    }
}

// Display error messages if there are any
if (!empty($errorMessages)) {
    foreach ($errorMessages as $errorMessage) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
}
