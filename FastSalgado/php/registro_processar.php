<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica o token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erro: Token CSRF inválido.");
    }

    // Remove o token após o uso
    unset($_SESSION['csrf_token']);
    
    // Processa o registro do usuário
    $nome = $_REQUEST["nome"];
    $email = $_REQUEST["email"];
    $senha = $_REQUEST["senha"];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar se o email já está em uso
        $verificaEmail = "SELECT * FROM usuarios WHERE email=?";
        $stmt = $conn->prepare($verificaEmail);
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row) {
            echo "Email já está em uso. Escolha outro.";
        } else {
            $senha_md5 = md5($senha);

            $inserirUsuario = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($inserirUsuario);
            $stmt->execute([$nome, $email, $senha_md5]);
            echo "Registro bem-sucedido. <a href='login.php'>Faça o login</a>";
        }

        $stmt = null;
        $conn = null;
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
