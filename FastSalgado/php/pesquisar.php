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

// Conexão ao Banco de Dados
try {
    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Verificar se o formulário de pesquisa foi submetido
if(isset($_POST['pesquisa'])) {
    $termo = $_POST['pesquisa'];

    // Preparar a consulta SQL para pesquisar em várias colunas
    $query = "SELECT * FROM imoveis WHERE 
                cidade LIKE :termo OR 
                endereco LIKE :termo OR 
                categoria LIKE :termo OR 
                preco LIKE :termo OR 
                nome_vendedor LIKE :termo OR 
                telefone_vendedor LIKE :termo OR 
                email_vendedor LIKE :termo OR 
                status LIKE :termo OR 
                descricao LIKE :termo OR
                latitude LIKE :termo OR 
                longitude LIKE :termo";

    // Preparar a declaração SQL
    $stmt = $conn->prepare($query);

    // Bind do parâmetro
    $termoPesquisa = "%$termo%";
    $stmt->bindParam(':termo', $termoPesquisa, PDO::PARAM_STR);

    // Executar a consulta
    $stmt->execute();

    // Obter resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastImóveis - Pesquisa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        .edit-icon { color: blue; }
        .delete-icon { color: red; }
        .icon-button { cursor: pointer; transition: color 0.2s; }
        /* Ajustes específicos para melhorar a responsividade da tabela */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            .table th, .table td {
                white-space: nowrap;
            }
        }
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
        <h2>Pesquisa de Imóveis</h2>

        <!-- Formulário de pesquisa -->
        <form method="POST" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Pesquisar..." name="pesquisa" aria-label="Pesquisar">
                <button class="btn btn-primary" type="submit">Pesquisar</button>
            </div>
        </form>

        <?php if(isset($resultados)): ?>
        <!-- Tabela de resultados com responsividade -->
        <div class="table-responsive">
            <table id="imoveisTable" class="table table-striped">
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
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($resultados as $row): ?>
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
                            <td><?= $row['latitude'] ?></td>
                            <td><?= $row['longitude'] ?></td>
                            <td>
                                <!-- Botão Visualizar -->
                                <a href="#" class="icon-button view-icon" data-bs-toggle="modal" data-bs-target="#viewModal-<?= $row['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <!-- View Modal -->
                        <div class="modal fade" id="viewModal-<?= $row['id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel-<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewModalLabel-<?= $row['id'] ?>">Detalhes do Imóvel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>ID:</strong> <?= $row['id'] ?></p>
                                        <p><strong>Endereço:</strong> <?= $row['endereco'] ?></p>
                                        <p><strong>Cidade:</strong> <?= $row['cidade'] ?></p>
                                        <p><strong>Categoria:</strong> <?= $row['categoria'] ?></p>
                                        <p><strong>Preço:</strong> <?= $row['preco'] ?></p>
                                        <p><strong>Nome do Vendedor:</strong> <?= $row['nome_vendedor'] ?></p>
                                        <p><strong>Telefone do Vendedor:</strong> <?= $row['telefone_vendedor'] ?></p>
                                        <p><strong>Email do Vendedor:</strong> <?= $row['email_vendedor'] ?></p>
                                        <p><strong>Status:</strong> <?= $row['status'] ?></p>
                                        <p><strong>Foto:</strong> <img src="<?= $row['foto'] ?>" alt="<?= $row['foto'] ?>" width="100"></p>
                                        <p><strong>Descrição:</strong> <?= $row['descricao'] ?></p>
                                        <p><strong>Latitude do Imóvel:</strong> <?= $row['latitude'] ?></p>
                                        <p><strong>Longitude do Imóvel:</strong> <?= $row['longitude'] ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json"></script>
</body>
</html>
