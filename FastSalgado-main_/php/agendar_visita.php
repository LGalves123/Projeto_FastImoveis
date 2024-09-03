<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION["nomeUsuario"])) {
    header("Location: login.php");
    exit();
}

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
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar corretores disponíveis
    $stmt = $conn->query("SELECT id, nome FROM corretores");
    $corretores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar visitas agendadas ordenadas pelo ID do imóvel
    $stmt_visitas = $conn->prepare("
        SELECT v.id AS visita_id, v.id_imovel, v.id_corretor, i.endereco AS imovel, c.nome AS corretor, v.data_visita, v.status
        FROM visitas v
        JOIN imoveis i ON v.id_imovel = i.id
        JOIN corretores c ON v.id_corretor = c.id
        ORDER BY v.id_imovel
        LIMIT :inicio, :total_reg");

    $stmt_visitas->bindParam(':inicio', $inicio, PDO::PARAM_INT);
    $stmt_visitas->bindParam(':total_reg', $total_reg, PDO::PARAM_INT);
    $stmt_visitas->execute();

    $total_records = $conn->query("SELECT COUNT(*) FROM visitas")->fetchColumn();
    $total_pages = ceil($total_records / $total_reg);

} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Visita - FastImóveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <!-- Formulário de Agendamento -->
        <h2>Agendar Visita</h2>
        <form id="agendarVisitaForm" action="agendar_visita_handler.php" method="post">
            <div class="mb-3">
                <label for="id_imovel" class="form-label">ID do Imóvel:</label>
                <input type="number" class="form-control" name="id_imovel" id="id_imovel" min="1" required>
                <small id="id_imovel_error" class="text-danger d-none">Imóvel não encontrado.</small>
            </div>
            <div class="mb-3">
                <label for="id_corretor" class="form-label">Selecione o Corretor:</label>
                <select class="form-select" name="id_corretor" required>
                    <option value="" disabled selected>Escolha um corretor</option>
                    <?php foreach ($corretores as $corretor): ?>
                        <option value="<?= $corretor['id'] ?>"><?= $corretor['nome'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="data_visita" class="form-label">Data da Visita:</label>
                <input type="datetime-local" class="form-control" name="data_visita" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select class="form-select" name="status" required>
                    <option value="Pendente">Pendente</option>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" id="submitButton">Agendar Visita</button>
        </form>

        <hr>

        <?php if ($isAdmin) { ?>
        <!-- Formulário de Cadastro de Corretores -->
        <h2 class="mt-5">Cadastrar Corretor</h2>
        <form id="cadastroCorretorForm" action="cadastrar_corretor_handler.php" method="post">
            <div class="mb-3">
                <label for="nome_corretor" class="form-label">Nome do Corretor:</label>
                <input type="text" class="form-control" name="nome_corretor" id="nome_corretor" required>
            </div>
            <button type="submit" class="btn btn-secondary">Cadastrar Corretor</button>
        </form>
        <?php } ?>

        <hr>

        <!-- Listagem de Visitas Agendadas -->
        <h2 class="mt-5">Visitas Agendadas</h2>
        <!-- Tornando a tabela responsiva -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID do Imóvel</th>
                        <th>Imóvel</th>
                        <th>Corretor</th>
                        <th>Data da Visita</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt_visitas->fetch(PDO::FETCH_ASSOC)) { ?>
                        <tr>
                            <td><?= $row['id_imovel'] ?></td>
                            <td><?= $row['imovel'] ?></td>
                            <td><?= $row['corretor'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['data_visita'])) ?></td>
                            <td><?= $row['status'] ?></td>
                            <td>
                                <!-- Botão de Visualização (Modal) -->
                                <a href="#" class="icon-button" data-bs-toggle="modal" data-bs-target="#viewModal-<?= $row['visita_id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($isAdmin) { ?>
                                    <!-- Botão de Edição (Modal) -->
                                    <a href="#" class="icon-button edit-icon" data-bs-toggle="modal" data-bs-target="#editModal-<?= $row['visita_id'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Botão de Exclusão -->
                                    <button class="icon-button delete-icon" onclick="deleteVisit(<?= $row['visita_id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                        <!-- Modal de Visualização -->
                        <div class="modal fade" id="viewModal-<?= $row['visita_id'] ?>" tabindex="-1" aria-labelledby="viewModalLabel-<?= $row['visita_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewModalLabel-<?= $row['visita_id'] ?>">Detalhes da Visita</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>ID do Imóvel:</strong> <?= $row['id_imovel'] ?></p>
                                        <p><strong>Imóvel:</strong> <?= $row['imovel'] ?></p>
                                        <p><strong>Corretor:</strong> <?= $row['corretor'] ?></p>
                                        <p><strong>Data da Visita:</strong> <?= date('d/m/Y H:i', strtotime($row['data_visita'])) ?></p>
                                        <p><strong>Status:</strong> <?= $row['status'] ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de Edição -->
                        <div class="modal fade" id="editModal-<?= $row['visita_id'] ?>" tabindex="-1" aria-labelledby="editModalLabel-<?= $row['visita_id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <form method="post" action="agendar_visita_handler.php">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel-<?= $row['visita_id'] ?>">Editar Visita</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="visitaId" value="<?= $row['visita_id'] ?>">
                                            <div class="mb-3">
                                                <label for="id_imovel" class="form-label">ID do Imóvel:</label>
                                                <input type="number" class="form-control" name="id_imovel" id="id_imovel" value="<?= $row['id_imovel'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="id_corretor" class="form-label">Selecione o Corretor:</label>
                                                <select class="form-select" name="id_corretor" required>
                                                    <option value="" disabled>Escolha um corretor</option>
                                                    <?php foreach ($corretores as $corretor): ?>
                                                        <option value="<?= $corretor['id'] ?>" <?= $row['id_corretor'] == $corretor['id'] ? 'selected' : '' ?>><?= $corretor['nome'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="data_visita" class="form-label">Data da Visita:</label>
                                                <input type="datetime-local" class="form-control" name="data_visita" value="<?= date('Y-m-d\TH:i', strtotime($row['data_visita'])) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status:</label>
                                                <select class="form-select" name="status" required>
                                                    <option value="Pendente" <?= $row['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                                    <option value="Confirmada" <?= $row['status'] == 'Confirmada' ? 'selected' : '' ?>>Confirmada</option>
                                                    <option value="Cancelada" <?= $row['status'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                        </div>
                                    </form>
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
                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $pagina - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class="page-item <?= $pagina == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php } ?>
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
        // Função para excluir visita
        function deleteVisit(visitId) {
            if (confirm("Tem certeza de que deseja excluir esta visita?")) {
                fetch('delete_visita.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'visitaId=' + encodeURIComponent(visitId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Visita excluída com sucesso!");
                        location.reload();
                    } else {
                        alert("Erro ao excluir visita: " + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Função para desabilitar fins de semana e horários fora de 08:00 às 19:00
        document.querySelector('input[type="datetime-local"]').addEventListener('input', function(event) {
            const inputDate = new Date(event.target.value);

            // Obtendo a data e hora local
            const day = inputDate.getDay(); // 0 = Domingo, 6 = Sábado
            const hours = inputDate.getHours(); // Hora local

            // Verifica se é fim de semana ou fora do horário permitido
            if (day === 0 || day === 6 || hours < 8 || hours >= 19) {
                alert('Selecione um horário de segunda a sexta, entre 08:00 e 19:00.');
                event.target.value = ''; // Reseta o valor do campo
            }
        });

        // Verificação de ID do Imóvel
        document.getElementById('id_imovel').addEventListener('blur', function() {
            const idImovel = this.value;

            if (idImovel > 0) {
                fetch('verificar_imovel.php?id_imovel=' + encodeURIComponent(idImovel))
                .then(response => response.json())
                .then(data => {
                    if (!data.exists) {
                        document.getElementById('id_imovel_error').classList.remove('d-none');
                        document.getElementById('submitButton').disabled = true;
                    } else {
                        document.getElementById('id_imovel_error').classList.add('d-none');
                        document.getElementById('submitButton').disabled = false;
                    }
                })
                .catch(error => console.error('Erro:', error));
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
