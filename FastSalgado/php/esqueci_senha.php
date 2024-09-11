<?php
session_start();

// Função para gerar o CSRF token
function gerarCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Gera um token aleatório
    }
    return $_SESSION['csrf_token'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Esqueci minha senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Recuperar Senha</h2>
    <form action="processa_recuperacao.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo gerarCsrfToken(); ?>">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar link de recuperação</button>
    </form>
</div>
</body>
</html>
