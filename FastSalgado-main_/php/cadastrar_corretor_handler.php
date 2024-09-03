<?php
session_start();

if (!isset($_SESSION["nomeUsuario"]) || !isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] != 1) {
    header("Location: login.php");
    exit();
}

try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nome_corretor = $_POST['nome_corretor'];

    $stmt = $conn->prepare("
        INSERT INTO corretores (nome)
        VALUES (:nome_corretor)");

    $stmt->bindParam(':nome_corretor', $nome_corretor);
    $stmt->execute();

    header("Location: cadastro_corretor.php");
    exit();

} catch (PDOException $e) {
    echo "Erro ao cadastrar corretor: " . $e->getMessage();
    exit();
}
?>
