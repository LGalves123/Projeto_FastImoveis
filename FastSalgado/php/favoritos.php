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

// Configurações de Paginação
$total_reg = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $total_reg;

// Conexão ao Banco de Dados
try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Favoritos - FastImóveis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    <!-- Conteúdo Principal -->
    <div class="container mt-5">
        <h2>Imóveis Favoritos</h2>

        <!-- Tornando a tabela responsiva -->
        <div class="table-responsive mt-4">
            <table id="favoritosTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Endereço</th>
                        <th>Cidade</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Nome do Vendedor</th>
                        <th>Telefone do Vendedor</th>
                        <th>Email do Vendedor</th>
                        <th>Status</th>
                        <th>Foto</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "
                        SELECT i.* FROM imoveis i
                        JOIN favoritos f ON i.id = f.imovel_id
                        WHERE f.usuario_id = :usuarioId
                        LIMIT $inicio, $total_reg";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':usuarioId', $usuarioId);
                    $stmt->execute();

                    $total_records = $conn->query("
                        SELECT COUNT(*) FROM favoritos 
                        WHERE usuario_id = $usuarioId")->fetchColumn();
                    $total_pages = ceil($total_records / $total_reg);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $imovelId = $row['id'];
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['endereco'] ?></td>
                            <td><?= $row['cidade'] ?></td>
                            <td><?= $row['categoria'] ?></td>
                            <td><?= $row['preco'] ?></td>
                            <td><?= $row['nome_vendedor'] ?></td>
                            <td><?= $row['telefone_vendedor'] ?></td>
                            <td><?= $row['email_vendedor'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td><img src="<?= $row['foto'] ?>" alt="<?= $row['foto'] ?>" width="100"></td>
                            <td><?= $row['descricao'] ?></td>
                            <td>
                                <!-- Botão Visualizar -->
                                <a href="#" class="icon-button view-icon" data-bs-toggle="modal" data-bs-target="#viewModal-<?= $row['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <!-- Botão Favoritar -->
                                <button class="icon-button" onclick="toggleFavorite(<?= $row['id'] ?>)">
                                    <i class="fas fa-star favorite-icon"></i>
                                </button>
                            </td>
                        </tr>
                        <!-- View Modal -->
                        <div class="modal fade" id="viewModal-<?= $row['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel-<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewModalLabel-<?= $row['id'] ?>">Detalhes do Imóvel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <img src="<?= $row['foto'] ?>" alt="<?= $row['foto'] ?>" class="img-fluid">
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

        <!-- Paginação -->
        <nav>
            <ul class="pagination justify-content-center">
                <!-- Botão para a página anterior -->
                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Números das páginas -->
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?= $pagina == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php } ?>

                <!-- Botão para a próxima página -->
                <li class="page-item <?= $pagina >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Scripts JavaScript -->
    <script>
        function deleteRecord(recordId) {
            if (confirm("Tem certeza de que deseja excluir este registro?")) {
                $.ajax({
                    type: "POST",
                    url: "deleteRecord.php",
                    data: {recordId: recordId},
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        }

        function toggleFavorite(imovelId) {
            fetch('toggleFavorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'imovelId=' + encodeURIComponent(imovelId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = document.querySelector(`button[onclick='toggleFavorite(${imovelId})'] i`);
                    icon.classList.toggle('fas');
                    icon.classList.toggle('far');
                }
                alert(data.message);
            })
            .catch(error => console.error('Error:', error));
        }
    </script>

    <!-- Carregamento do jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
</body>
</html>
