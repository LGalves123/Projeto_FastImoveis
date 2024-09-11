<?php
// Conexão com o banco de dados
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";

// Verifica se o parâmetro 'id' foi fornecido na URL
if (isset($_GET['id'])) {
    // Obtém o ID da imagem da URL e evita injeção de SQL
    $imageId = intval($_GET['id']);

    try {
        // Connect to the database
        $conn = new PDO($dbUrl, $dbUser, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepara e executa a consulta SQL para recuperar a imagem do banco de dados
        $stmt = $conn->prepare("SELECT foto FROM imoveis WHERE id = ?");
        $stmt->execute([$imageId]);

        // Verifica se a imagem foi encontrada
        if ($stmt->rowCount() > 0) {
            // Define o cabeçalho Content-Type para indicar que a saída é uma imagem
            header("Content-Type: image/jpeg");

            // Obtém os dados binários da imagem do resultado da consulta
            $imageData = $stmt->fetchColumn();

            // Adiciona o prefixo 'img/' ao caminho retornado do banco de dados
            $imagePath = 'img/' . $imageData;

            // Envia os dados binários da imagem como saída
            readfile($imagePath);
        } else {
            // Se a imagem não for encontrada, exibe uma imagem padrão ou retorna um erro
            // Exemplo de exibição de uma imagem padrão:
            // $defaultImage = file_get_contents("caminho_para_imagem_padrao.jpg");
            // header("Content-Type: image/jpeg");
            // echo $defaultImage;

            // Ou, se preferir, pode retornar um erro 404:
            // header("HTTP/1.0 404 Not Found");
            // echo "Imagem não encontrada.";
        }
    } catch (PDOException $e) {
        // Em caso de erro na conexão ou na consulta SQL
        echo "Erro: " . $e->getMessage();
    } finally {
        // Fecha a conexão com o banco de dados
        $conn = null;
    }
} else {
    // Se o parâmetro 'id' não foi fornecido na URL, retorna um erro
    header("HTTP/1.0 400 Bad Request");
    echo "Parâmetro 'id' não fornecido.";
}
?>
