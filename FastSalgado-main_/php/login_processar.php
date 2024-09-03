<?php
$email = $_REQUEST["email"];
$senha = $_REQUEST["senha"];

try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criptografa a senha fornecida pelo usuário usando MD5
    $senha_md5 = md5($senha);

    $verificarUsuario = "SELECT * FROM usuarios WHERE email=? AND senha=?";
    $stmt = $conn->prepare($verificarUsuario);
    // Usa a senha criptografada em MD5
    $stmt->execute([$email, $senha_md5]);
    $row = $stmt->fetch();

    if ($row) {
        $nomeUsuario = $row["nome"];
        $idUsuario = $row["id"]; // Obtém o ID do usuário
        $isAdmin = $row["isAdmin"]; // Adiciona isAdmin à sessão se o login for bem-sucedido
        session_start();
        $_SESSION["nomeUsuario"] = $nomeUsuario;
        $_SESSION["idUsuario"] = $idUsuario; // Adiciona o ID do usuário à sessão
        $_SESSION["isAdmin"] = $isAdmin; // Adiciona isAdmin à sessão

        // Verifica se é conta de administrador
        if ($email === "adminbr@gmail.com") {
            header("Location: painel.php");
        } else {
            header("Location: painel.php");
        }
        exit(); // Certifique-se de sair após o redirecionamento
    } else {
        echo "Login falhou. Verifique suas credenciais. <a href='login.php'>Tentar novamente</a>";
    }

    $stmt = null;
    $conn = null;
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
