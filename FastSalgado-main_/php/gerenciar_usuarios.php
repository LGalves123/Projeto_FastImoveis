<?php

session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION["nomeUsuario"])) {
    header("Location: login.php");
    exit();
}

// Verificar expiração da sessão
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION['last_activity'] = time();

$nomeUsuario = $_SESSION["nomeUsuario"];
$isAdmin = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1;

// Conexão ao Banco de Dados
try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Verificar se uma ação de exclusão foi solicitada
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $usuarioId = $_GET['id'];

        try {
            $query = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $usuarioId);
            $stmt->execute();

            header("Location: gerenciar_usuarios.php");
            exit();
        } catch (PDOException $e) {
            echo "Erro ao tentar excluir o usuário: " . $e->getMessage();
            exit();
        }
    } else {
        header("Location: gerenciar_usuarios.php");
        exit();
    }
}

$usuarioAdminLogado = 'João';

$query = "SELECT * FROM usuarios WHERE ativo = 1 AND nome != :admin";
$stmt = $conn->prepare($query);
$stmt->bindParam(':admin', $usuarioAdminLogado);
$stmt->execute();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - FastImóveis</title>
    <!-- Incluir CSS do Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        .edit-icon { color: blue; }
        .delete-icon { color: red; }
        .icon-button { cursor: pointer; transition: color 0.2s; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand mt-2 mt-lg-0" href="painel.php">FastImóveis</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="pesquisar.php">Pesquisar</a>
                    </li>
                    <?php if ($isAdmin) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="gerenciar_usuarios.php">Usuários</a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="favoritos.php">Favoritos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="agendar_visita.php">Visitas</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <h5 class="mt-3 me-3 text-white">Bem-vindo, <?= $nomeUsuario ?>!</h5>
                        <a href="../arquivos/index.php" class="btn btn-outline-light">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Gerenciar Usuários</h2>

        <!-- Tornando a tabela responsiva -->
        <div class="table-responsive">
            <table id="usuariosTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nome'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td>
                                <!-- Botões para abrir modais -->
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalDetalhes<?= $row['id'] ?>">Detalhes</button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id'] ?>">Editar</button>
                                <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                            </td>
                        </tr>

                        <!-- Modal de Detalhes -->
                        <div class="modal fade" id="modalDetalhes<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalDetalhesLabel<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalDetalhesLabel<?= $row['id'] ?>">Detalhes do Usuário</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>ID:</strong> <?= $row['id'] ?></p>
                                        <p><strong>Nome:</strong> <?= $row['nome'] ?></p>
                                        <p><strong>Email:</strong> <?= $row['email'] ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de Edição -->
                        <div class="modal fade" id="modalEditar<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalEditarLabel<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditarLabel<?= $row['id'] ?>">Editar Usuário</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Formulário de Edição -->
                                        <form method="post" action="editRecord.php" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <div class="mb-3">
                                                <label for="nome" class="form-label">Nome:</label>
                                                <input type="text" class="form-control" id="nome" name="nome" value="<?= $row['nome'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email:</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?= $row['email'] ?>" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Incluir scripts JavaScript necessários (jQuery, Bootstrap JS) -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn = null;
?>
