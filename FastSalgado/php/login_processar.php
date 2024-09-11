<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o token CSRF está presente e é válido
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erro: Token CSRF inválido.");
    }

    // Após o uso, remove o token da sessão
    unset($_SESSION['csrf_token']);
    
    // Continua o processamento normal do login
    $email = $_REQUEST["email"];
    $senha = $_REQUEST["senha"];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $senha_md5 = md5($senha);

        $verificarUsuario = "SELECT * FROM usuarios WHERE email=? AND senha=?";
        $stmt = $conn->prepare($verificarUsuario);
        $stmt->execute([$email, $senha_md5]);
        $row = $stmt->fetch();

        if ($row) {
            $_SESSION["nomeUsuario"] = $row["nome"];
            $_SESSION["idUsuario"] = $row["id"];
            $_SESSION["isAdmin"] = $row["isAdmin"];
            header("Location: painel.php");
        } else {
            echo "Login falhou. Verifique suas credenciais. <a href='login.php'>Tentar novamente</a>";
        }

        $stmt = null;
        $conn = null;
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
