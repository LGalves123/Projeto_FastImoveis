<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["idUsuario"])) {
    header("Location: login.php");
    exit();
}

$usuarioId = $_SESSION["idUsuario"];
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";

$targetDir = "img/";
$errorMessages = [];

try {
    // Conectar ao banco de dados
    $conn = new PDO($dbUrl, $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar o número de solicitações pendentes
    $queryPendentes = "SELECT COUNT(*) FROM solicitacoes_imoveis WHERE usuario_id = :usuarioId AND status = 'pendente'";
    $stmtPendentes = $conn->prepare($queryPendentes);
    $stmtPendentes->bindParam(':usuarioId', $usuarioId);
    $stmtPendentes->execute();
    $solicitacoesPendentes = $stmtPendentes->fetchColumn();

    if ($solicitacoesPendentes >= 3) {
        $errorMessages[] = "Você atingiu o limite de 3 solicitações pendentes. Aguarde a aprovação para novas solicitações.";
    } else {
        // Dados do formulário
        $endereco = $_POST["endereco"] ?? "";
        $cidade = $_POST["cidade"] ?? "";
        $categoria = $_POST["categoria"] ?? "";
        $preco = $_POST["preco"] ?? "";
        $nome_vendedor = $_POST["nome_vendedor"] ?? "";
        $telefone_vendedor = $_POST["telefone_vendedor"] ?? "";
        $email_vendedor = $_POST["email_vendedor"] ?? "";
        $descricao = $_POST["descricao"] ?? "";
        $status = 'pendente'; // Status padrão
        $latitude = $_POST['latitude'] ?? null;
        $longitude = $_POST['longitude'] ?? null;

        // Validar campos obrigatórios
        if (empty($endereco) || empty($cidade) || empty($categoria) || empty($preco) || empty($nome_vendedor) || empty($telefone_vendedor) || empty($email_vendedor)) {
            $errorMessages[] = "Por favor, preencha todos os campos obrigatórios.";
        }

        // Lógica de upload de arquivo
        $targetFile = $targetDir . basename($_FILES["foto"]["name"]);
        $uploadOk = 1;

        if ($_FILES["foto"]["size"] > 5000000) {
            $errorMessages[] = "Desculpe, o arquivo é muito grande.";
            $uploadOk = 0;
        }

        $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
        $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errorMessages[] = "Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
            $uploadOk = 0;
        }

        // Mover o arquivo e inserir a solicitação
        if ($uploadOk == 1 && move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
            $sql = "INSERT INTO solicitacoes_imoveis (usuario_id, endereco, cidade, categoria, preco, nome_vendedor, telefone_vendedor, email_vendedor, status, foto, descricao, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$usuarioId, $endereco, $cidade, $categoria, $preco, $nome_vendedor, $telefone_vendedor, $email_vendedor, $status, $targetFile, $descricao, $latitude, $longitude]);

            if ($stmt->rowCount() > 0) {
                header("Location: painel.php");
                exit();
            } else {
                $errorMessages[] = "Erro ao adicionar o registro.";
            }
        } else {
            $errorMessages[] = "Erro ao enviar o arquivo.";
        }
    }
} catch (PDOException $e) {
    $errorMessages[] = "Erro: " . $e->getMessage();
}

// Exibe as mensagens de erro, se houver
if (!empty($errorMessages)) {
    foreach ($errorMessages as $errorMessage) {
        echo "<p style='color: red;'>$errorMessage</p>";
    }
}
