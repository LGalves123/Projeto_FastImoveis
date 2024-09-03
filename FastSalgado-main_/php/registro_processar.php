<?php
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
        // Criptografa a senha fornecida pelo usuário usando MD5
        $senha_md5 = md5($senha);

        $inserirUsuario = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($inserirUsuario);
        // Insere a senha criptografada em MD5 no banco de dados
        $stmt->execute([$nome, $email, $senha_md5]);
        echo "Registro bem-sucedido. <a href='login.php'>Faça o login</a>";
    }

    $stmt = null;
    $conn = null;
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
