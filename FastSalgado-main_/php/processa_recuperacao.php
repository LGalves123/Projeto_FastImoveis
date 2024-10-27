<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erro: Token CSRF inválido.");
    }

    unset($_SESSION['csrf_token']);

    $email = $_POST['email'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $verificarUsuario = "SELECT * FROM usuarios WHERE email=?";
        $stmt = $conn->prepare($verificarUsuario);
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row) {
            $token = bin2hex(random_bytes(32));
            $url = "http://localhost/fastimoveis/redefinir_senha.php?token=" . $token;
            
            $stmt = $conn->prepare("UPDATE usuarios SET reset_token=?, token_expira=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=?");
            $stmt->execute([$token, $email]);
            
            $assunto = "Redefinição de senha - FastImóveis";
            $mensagem = "Clique no link para redefinir sua senha: " . $url;
            $headers = "From: no-reply@fastimoveis.com.br\r\n";
            
            mail($email, $assunto, $mensagem, $headers);

            echo "Um link de redefinição de senha foi enviado para seu email.";
        } else {
            echo "Email não encontrado.";
        }
        
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
