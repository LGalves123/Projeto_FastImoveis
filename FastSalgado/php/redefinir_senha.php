<?php
session_start();

if (!isset($_GET['token'])) {
    die("Token inválido.");
}

$token = $_GET['token'];

try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE reset_token=? AND token_expira > NOW()");
    $stmt->execute([$token]);
    $row = $stmt->fetch();

    if (!$row) {
        die("Token inválido ou expirado.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nova_senha = $_POST['nova_senha'];
        $nova_senha_md5 = md5($nova_senha);

        $stmt = $conn->prepare("UPDATE usuarios SET senha=?, reset_token=NULL, token_expira=NULL WHERE id=?");
        $stmt->execute([$nova_senha_md5, $row['id']]);

        echo "Senha alterada com sucesso. <a href='login.php'>Faça login</a>";
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Redefinir Senha</h2>
    <form action="" method="post">
        <div class="mb-3">
            <label for="nova_senha" class="form-label">Nova Senha</label>
            <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
        </div>
        <button type="submit" class="btn btn-primary">Redefinir Senha</button>
    </form>
</div>
</body>
</html>
