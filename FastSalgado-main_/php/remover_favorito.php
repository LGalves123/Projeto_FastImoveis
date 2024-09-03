<?php
session_start();

if (!isset($_SESSION["nomeUsuario"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id_imovel']) && is_numeric($_GET['id_imovel'])) {
    $id_imovel = $_GET['id_imovel'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Excluir o imóvel dos favoritos do usuário atual
        $query = "DELETE FROM favoritos WHERE id_usuario = :id_usuario AND id_imovel = :id_imovel";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_usuario', $_SESSION['idUsuario']);
        $stmt->bindParam(':id_imovel', $id_imovel);
        $stmt->execute();

        header("Location: favoritos.php");
        exit();
    } catch (PDOException $e) {
        echo "Erro na conexão ou consulta: " . $e->getMessage();
    }
} else {
    echo "ID do imóvel inválido.";
}
?>
