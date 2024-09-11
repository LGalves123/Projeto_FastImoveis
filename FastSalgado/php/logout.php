<?php
session_start();

// Verifica se o usuário está logado (isso depende da lógica de autenticação implementada)
if (isset($_SESSION['usuario_id'])) {
    $usuarioId = $_SESSION['usuario_id'];

    try {
        // Conexão com o banco de dados (assumindo que já está conectado)
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");

        // Atualiza o status do usuário para inativo
        $query = "UPDATE usuarios SET ativo = 0 WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $usuarioId);
        $stmt->execute();

        // Limpa e destrói a sessão
        session_unset();
        session_destroy();

        // Redireciona para a página de login
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
        exit();
    }
} else {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: login.php");
    exit();
}
?>
