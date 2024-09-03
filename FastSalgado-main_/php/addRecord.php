<?php
// Database connection parameters
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";

// Retrieve form data from request
$endereco = $_POST["endereco"] ?? "";
$cidade = $_POST["cidade"] ?? "";
$categoria = $_POST["categoria"] ?? "";
$preco = $_POST["preco"] ?? "";
$nome_vendedor = $_POST["nome_vendedor"] ?? "";
$telefone_vendedor = $_POST["telefone_vendedor"] ?? "";
$email_vendedor = $_POST["email_vendedor"] ?? "";
$descricao = $_POST["descricao"] ?? "";
$status = $_POST["status"] ?? "";

// File upload parameters
$targetDir = "img/"; // Directory where uploaded files will be stored
$targetFile = $targetDir . basename($_FILES["foto"]["name"]); // Path of the target file
$uploadOk = 1; // Flag to indicate if the file was uploaded successfully

// Check if file already exists
if (file_exists($targetFile)) {
    echo "Desculpe, o arquivo já existe.";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["foto"]["size"] > 5000000) { // Adjust the file size limit as needed
    echo "Desculpe, o arquivo é muito grande.";
    $uploadOk = 0;
}

// Allow certain file formats
$allowedExtensions = ["jpg", "jpeg", "png", "gif"]; // Add or remove file extensions as needed
$fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
if (!in_array($fileExtension, $allowedExtensions)) {
    echo "Desculpe, apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Desculpe, o arquivo não foi enviado.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
        // File uploaded successfully, proceed with database insertion

        try {
            // Connect to the database
            $conn = new PDO($dbUrl, $dbUser, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL query to insert a new record
            $sql = "INSERT INTO imoveis (endereco, cidade, categoria, preco, nome_vendedor, telefone_vendedor, email_vendedor, status, foto, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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

            // Execute the SQL query to insert the new record
            $stmt->execute();

            $rowsAffected = $stmt->rowCount();

            if ($rowsAffected > 0) {
                // Record was successfully added
                header("Location: painel.php");
                exit();
            } else {
                // Record insertion failed
                header("Location: your_failure_page.php");
                exit();
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            header("Location: your_error_page.php");
            exit();
        } finally {
            // Close resources
            $stmt = null;
            $conn = null;
        }
    } else {
        echo "Desculpe, ocorreu um erro ao enviar o arquivo.";
    }
}
?>
<?php
// Database connection parameters
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";

// Retrieve form data from request
$endereco = $_POST["endereco"] ?? "";
$cidade = $_POST["cidade"] ?? "";
$categoria = $_POST["categoria"] ?? "";
$preco = $_POST["preco"] ?? "";
$nome_vendedor = $_POST["nome_vendedor"] ?? "";
$telefone_vendedor = $_POST["telefone_vendedor"] ?? "";
$email_vendedor = $_POST["email_vendedor"] ?? "";
$descricao = $_POST["descricao"] ?? "";
$status = $_POST["status"] ?? "";

// File upload parameters
$targetDir = "img/"; // Directory where uploaded files will be stored
$targetFile = $targetDir . basename($_FILES["foto"]["name"]); // Path of the target file
$uploadOk = 1; // Flag to indicate if the file was uploaded successfully

// Check if file already exists
if (file_exists($targetFile)) {
    echo "Desculpe, o arquivo já existe.";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["foto"]["size"] > 5000000) { // Adjust the file size limit as needed
    echo "Desculpe, o arquivo é muito grande.";
    $uploadOk = 0;
}

// Allow certain file formats
$allowedExtensions = ["jpg", "jpeg", "png", "gif"]; // Add or remove file extensions as needed
$fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
if (!in_array($fileExtension, $allowedExtensions)) {
    echo "Desculpe, apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Desculpe, o arquivo não foi enviado.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
        // File uploaded successfully, proceed with database insertion

        try {
            // Connect to the database
            $conn = new PDO($dbUrl, $dbUser, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL query to insert a new record
            $sql = "INSERT INTO imoveis (endereco, cidade, categoria, preco, nome_vendedor, telefone_vendedor, email_vendedor, status, foto, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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

            // Execute the SQL query to insert the new record
            $stmt->execute();

            $rowsAffected = $stmt->rowCount();

            if ($rowsAffected > 0) {
                // Record was successfully added
                header("Location: painel.php");
                exit();
            } else {
                // Record insertion failed
                header("Location: your_failure_page.php");
                exit();
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            header("Location: your_error_page.php");
            exit();
        } finally {
            // Close resources
            $stmt = null;
            $conn = null;
        }
    } else {
        echo "Desculpe, ocorreu um erro ao enviar o arquivo.";
    }
}
?>
<?php
// Database connection parameters
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";

// Retrieve form data from request
$endereco = $_POST["endereco"] ?? "";
$cidade = $_POST["cidade"] ?? "";
$categoria = $_POST["categoria"] ?? "";
$preco = $_POST["preco"] ?? "";
$nome_vendedor = $_POST["nome_vendedor"] ?? "";
$telefone_vendedor = $_POST["telefone_vendedor"] ?? "";
$email_vendedor = $_POST["email_vendedor"] ?? "";
$descricao = $_POST["descricao"] ?? "";
$status = $_POST["status"] ?? "";

// File upload parameters
$targetDir = "img/"; // Directory where uploaded files will be stored
$targetFile = $targetDir . basename($_FILES["foto"]["name"]); // Path of the target file
$uploadOk = 1; // Flag to indicate if the file was uploaded successfully

// Check if file already exists
if (file_exists($targetFile)) {
    echo "Desculpe, o arquivo já existe.";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["foto"]["size"] > 5000000) { // Adjust the file size limit as needed
    echo "Desculpe, o arquivo é muito grande.";
    $uploadOk = 0;
}

// Allow certain file formats
$allowedExtensions = ["jpg", "jpeg", "png", "gif"]; // Add or remove file extensions as needed
$fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
if (!in_array($fileExtension, $allowedExtensions)) {
    echo "Desculpe, apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Desculpe, o arquivo não foi enviado.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
        // File uploaded successfully, proceed with database insertion

        try {
            // Connect to the database
            $conn = new PDO($dbUrl, $dbUser, $dbPassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // SQL query to insert a new record
            $sql = "INSERT INTO imoveis (endereco, cidade, categoria, preco, nome_vendedor, telefone_vendedor, email_vendedor, status, foto, descricao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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

            // Execute the SQL query to insert the new record
            $stmt->execute();

            $rowsAffected = $stmt->rowCount();

            if ($rowsAffected > 0) {
                // Record was successfully added
                header("Location: painel.php");
                exit();
            } else {
                // Record insertion failed
                header("Location: your_failure_page.php");
                exit();
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            header("Location: your_error_page.php");
            exit();
        } finally {
            // Close resources
            $stmt = null;
            $conn = null;
        }
    } else {
        echo "Desculpe, ocorreu um erro ao enviar o arquivo.";
    }
}
?>
