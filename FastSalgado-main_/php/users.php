<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>FastImóveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .edit-icon {
            color: blue;
        }

        .delete-icon {
            color: red;
        }

        .icon-button {
            cursor: pointer;
            transition: color 0.2s;
        }
    </style>
</head>

<body>
    <?php
    $nomeUsuario = $_SESSION["nomeUsuario"];
    if ($nomeUsuario != null) {
    ?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <!-- Container wrapper -->
        <div class="container-fluid">
            <!-- Toggle button -->
            <button
                class="navbar-toggler"
                type="button"
                data-mdb-toggle="collapse"
                data-mdb-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <i class="fas fa-bars"></i>
            </button>

            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Navbar brand -->
                <a class="navbar-brand mt-2 mt-lg-0" href="#">
                    <img
                        src="/fastimoveis/assets/logo.webp"
                        height="15"
                        alt="MDB Logo"
                        loading="lazy"
                    />
                </a>
            </div>
            
            <!-- Right elements -->
            <div class="d-flex align-items-center">
                <!-- Icon -->
                <a class="text-reset me-3" href="#">
                    <i class="fas fa-shopping-cart"></i>
                </a>
               
                <div class="dropdown">
                    <a>
                        <h5 class="mt-3" style="color: white;">Bem-vindo, <?= $nomeUsuario ?>!</h5>
                    </a>
                       
                    <div class="ms-3">
                        <a href="logout.php" class="btn btn-outline-light">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Body content -->
    <div class="container">
        <h2 class="mt-5">Listagem de Imóveis</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            Adicionar novo
        </button>

        <br>
        <br>

        <!-- Add Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
<!-- Add Modal content -->
<form method="post" action="addRecord.php">
    <div class="modal-header">
        <h5 class="modal-title" id="addModalLabel">Adicionar Novo Imóvel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço:</label>
            <input type="text" class="form-control" id="endereco" name="endereco">
        </div>
        <div class="mb-3">
            <label for="categoria" class="form-label">Categoria:</label>
            <select class="form-select" id="categoria" name="categoria">
                <option value="Casa">Casa</option>
                <option value="Apartamento">Apartamento</option>
                <option value="Kitnet">Kitnet</option>
                <option value="Sobrado">Sobrado</option>
                <option value="Mansão">Mansão</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço:</label>
            <input type="text" class="form-control" id="preco" name="preco">
        </div>
        <div class="mb-3">
            <label for="nome_vendedor" class="form-label">Nome do Vendedor (Proprietário):</label>
            <textarea class="form-control" id="nome_vendedor" name="nome_vendedor" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="telefone_vendedor" class="form-label">Telefone do Vendedor (Proprietário):</label>
            <textarea class="form-control" id="telefone_vendedor" name="telefone_vendedor" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="email_vendedor" class="form-label">Email do Vendedor (Proprietário):</label>
            <textarea class="form-control" id="email_vendedor" name="email_vendedor" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select class="form-select" id="status" name="status">
                <option value="à_venda">à venda</option>
                <option value="alugar">alugar</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary">Adicionar</button>
    </div>
</form>

                </div>
            </div>
        </div>

        <table id="imoveisTable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Endereço</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Nome do Vendedor</th>
                    <th>Telefone do Vendedor</th>
                    <th>Email do Vendedor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
                    $query = "SELECT * FROM imoveis";
                    $stmt = $conn->query($query);

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['endereco'] ?></td>
                        <td><?= $row['categoria'] ?></td>
                        <td><?= $row['preco'] ?></td>
                        <td><?= $row['nome_vendedor'] ?></td>
                        <td><?= $row['telefone_vendedor'] ?></td>
                        <td><?= $row['email_vendedor'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <i class="edit-icon icon-button" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </i>

                            <i class="delete-icon" type="button" class="btn btn-icon btn-danger" onclick="deleteRecord(<?= $row['id'] ?>)">
                                <i class="fas fa-trash"></i>
                            </i>
                        </td>
                    </tr>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $row['id'] ?>" aria-hidden="true">
                        <!-- Delete Modal content -->
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                        <!-- Edit Modal content -->
                    </div>
                <?php
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function deleteRecord(recordId) {
            if (confirm("Are you sure you want to delete this record?")) {
                // AJAX delete request
                $.ajax({
                    type: "POST",
                    url: "deleteRecord.php",
                    data: {recordId: recordId},
                    success: function (data) {
                        // Handle the response (e.g., show a success message or refresh the page)
                        location.reload(); // Refresh the page
                    }
                });
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json"></script>
    <script>
        $(document).ready(function () {
            $('#imoveisTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese-Brasil.json"
                },
                "responsive": true
            });
        });
    </script>
</body>
<?php } // Add this closing brace ?>

</html>
