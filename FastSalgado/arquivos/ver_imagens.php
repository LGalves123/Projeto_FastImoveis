<?php
if (isset($_GET['tipo']) && isset($_GET['id'])) {
    $tipo = $_GET['tipo'];
    $imovel_id = $_GET['id'];

    $dbHost = "localhost";
    $dbName = "fastimoveis";
    $dbUser = "root";
    $dbPass = "";

    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT $tipo FROM imoveis WHERE id = ?");
        $stmt->execute([$imovel_id]);

        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $imagePath = $result[$tipo];

            if (!empty($imagePath) && file_exists($imagePath)) {
                echo "<img src='$imagePath' alt='Imagem de $tipo'>";
            } else {
                echo "Imagem não encontrada.";
            }
        } else {
            echo "Imóvel não encontrado.";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
    $conn = null;
} else {
    echo "Parâmetros inválidos.";
}
?>
