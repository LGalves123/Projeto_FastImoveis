<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o token CSRF está presente e é válido
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erro: Token CSRF inválido.");
    }

    // Após o uso, remove o token da sessão
    unset($_SESSION['csrf_token']);

    // Verifica se o campo CRECI foi preenchido
    $creci = $_POST["creci"] ?? null;

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (!empty($creci)) {
            // Caso o CRECI seja preenchido, realiza o login com o número CRECI
            $verificarCorretor = "SELECT * FROM usuarios WHERE CRECI = ?";
            $stmt = $conn->prepare($verificarCorretor);
            $stmt->execute([$creci]);
            $row = $stmt->fetch();

            if ($row) {
                $_SESSION["nomeUsuario"] = $row["nome"];
                $_SESSION["idUsuario"] = $row["id"];
                $_SESSION["isAdmin"] = $row["isAdmin"];
                $_SESSION["isCorretor"] = $row["isCorretor"];
                header("Location: painel.php");
                exit();
            } else {
                echo "Número CRECI não encontrado. <a href='login.php'>Tentar novamente</a>";
                exit();
            }
        } else {
            // Caso o CRECI não seja preenchido, realiza o login com email e senha
            $email = $_POST["email"];
            $senha = $_POST["senha"];
            $senha_md5 = md5($senha);

            $verificarUsuario = "SELECT * FROM usuarios WHERE email=? AND senha=?";
            $stmt = $conn->prepare($verificarUsuario);
            $stmt->execute([$email, $senha_md5]);
            $row = $stmt->fetch();

            if ($row) {
                $_SESSION["nomeUsuario"] = $row["nome"];
                $_SESSION["idUsuario"] = $row["id"];
                $_SESSION["isAdmin"] = $row["isAdmin"];
                $_SESSION["isCorretor"] = $row["isCorretor"];
                header("Location: painel.php");
            } else {
                echo "Login falhou. Verifique suas credenciais. <a href='login.php'>Tentar novamente</a>";
            }
        }

        $stmt = null;
        $conn = null;
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
