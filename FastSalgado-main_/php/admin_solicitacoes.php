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
$usuarioId = $_SESSION["idUsuario"];
$isAdmin = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1;
$isCorretor = isset($_SESSION["isCorretor"]) && $_SESSION["isCorretor"] == 1;

// Configurações de Paginação
$total_reg = 5; // Número de registros por página
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $total_reg;

// Conexão ao Banco de Dados
try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para buscar solicitações com limite para paginação
    $sql = "SELECT * FROM solicitacoes_imoveis LIMIT :inicio, :total_reg";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    $stmt->bindParam(':total_reg', $total_reg, PDO::PARAM_INT);
    $stmt->execute();
    $solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Contar o total de registros para a paginação
    $total_records = $conn->query("SELECT COUNT(*) FROM solicitacoes_imoveis")->fetchColumn();
    $total_pages = ceil($total_records / $total_reg);

} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit();
}

// Lógica para aprovação/rejeição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = $_POST['id'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $requestSql = "SELECT * FROM solicitacoes_imoveis WHERE id = ?";
        $requestStmt = $conn->prepare($requestSql);
        $requestStmt->execute([$id]);
        $solicitacao = $requestStmt->fetch(PDO::FETCH_ASSOC);

        if ($solicitacao) {
            if ($_POST['action'] === 'approve') {
                $insertSql = "INSERT INTO imoveis (endereco, cidade, categoria, preco, nome_vendedor, telefone_vendedor, email_vendedor, foto, descricao, status, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'disponível', ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->execute([
                    $solicitacao['endereco'],
                    $solicitacao['cidade'],
                    $solicitacao['categoria'],
                    $solicitacao['preco'],
                    $solicitacao['nome_vendedor'],
                    $solicitacao['telefone_vendedor'],
                    $solicitacao['email_vendedor'],
                    $solicitacao['foto'],
                    $solicitacao['descricao'],
                    $solicitacao['latitude'],
                    $solicitacao['longitude']
                ]);
                $deleteSql = "DELETE FROM solicitacoes_imoveis WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->execute([$id]);
            } else {
                $updateSql = "UPDATE solicitacoes_imoveis SET status = ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->execute(['rejeitado', $id]);
            }
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $requestStmt = null;
        $insertStmt = null;
        $deleteStmt = null;
        $updateStmt = null;
        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações de Imóveis - FastImóveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <li class="nav-item">
                            <a class="nav-link" href="admin_solicitacoes.php">Solicitações</a>
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

    <!-- Conteúdo da página -->
    <div class="container mt-5">
        <h2>Solicitações de Imóveis</h2>

        <!-- Tabela de Solicitações -->
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Endereço</th>
                    <th>Cidade</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Descrição</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitacoes as $solicitacao) { ?>
                    <tr>
                        <td><?= $solicitacao['id'] ?></td>
                        <td><?= $solicitacao['endereco'] ?></td>
                        <td><?= $solicitacao['cidade'] ?></td>
                        <td><?= $solicitacao['categoria'] ?></td>
                        <td><?= $solicitacao['preco'] ?></td>
                        <td><?= $solicitacao['descricao'] ?></td>
                        <td><?= $solicitacao['telefone_vendedor'] ?></td>
                        <td><?= $solicitacao['email_vendedor'] ?></td>
                        <td><?= $solicitacao['status'] ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $solicitacao['id'] ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success">Aprovar</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $solicitacao['id'] ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Rejeitar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Paginação -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?= $pagina == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?= $pagina >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>" aria-label="Próximo">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
