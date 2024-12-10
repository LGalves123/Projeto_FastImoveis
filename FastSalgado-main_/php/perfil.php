<?php
session_start();


$usuarioId = $_SESSION["idUsuario"];
$nomeUsuario = $_SESSION["nomeUsuario"];
$dbUrl = "mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4";
$dbUser = "root";
$dbPassword = "";
$conn = new PDO($dbUrl, $dbUser, $dbPassword);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Configurações de Paginação
$total_reg = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $total_reg;

$isAdmin = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1;
$isCorretor = isset($_SESSION["isCorretor"]) && $_SESSION["isCorretor"] == 1;

// Obter email, telefone, celular, imobiliária e foto de perfil do usuário
$stmt = $conn->prepare("SELECT email, telefone, celular, imobiliaria, github, twitter, instagram, facebook, foto_perfil FROM usuarios WHERE id = :usuarioId");
$stmt->execute([':usuarioId' => $usuarioId]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$emailUsuario = $userData['email'];
$telefoneUsuario = $userData['telefone'] ?: '';
$celularUsuario = $userData['celular'] ?: '';
$imobiliariaUsuario = $userData['imobiliaria'] ?: '';
$githubUsuario = $userData['github'] ?: '';
$twitterUsuario = $userData['twitter'] ?: '';
$instagramUsuario = $userData['instagram'] ?: '';
$facebookUsuario = $userData['facebook'] ?: '';
$fotoPerfil = $userData['foto_perfil'] ?: 'img/default_profile.png';

// Processa o formulário do modal para atualizar o telefone, celular, imobiliária, github, twitter, instagram e facebook
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_info"])) {
    $telefone = $_POST["telefone"] ?? '';
    $celular = $_POST["celular"] ?? '';
    $imobiliaria = $_POST["imobiliaria"] ?? '';
    $github = $_POST["github"] ?? '';
    $twitter = $_POST["twitter"] ?? '';
    $instagram = $_POST["instagram"] ?? '';
    $facebook = $_POST["facebook"] ?? '';

    $sql = "UPDATE usuarios SET telefone = :telefone, celular = :celular, imobiliaria = :imobiliaria,  github = :github, twitter = :twitter, instagram = :instagram, facebook = :facebook WHERE id = :usuarioId";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':telefone' => $telefone,
        ':celular' => $celular,
        ':imobiliaria' => $imobiliaria,
        ':github' => $github, 
        ':twitter' => $twitter, 
        ':instagram' => $instagram, 
        ':facebook' => $facebook, 
        ':usuarioId' => $usuarioId
    ]);
    
    // Atualiza os dados em variáveis para exibição imediata
    $telefoneUsuario = $telefone;
    $celularUsuario = $celular;
    $imobiliariaUsuario = $imobiliaria;
    $githubUsuario = $github;
    $twitterUsuario = $twitter;
    $instagramUsuario = $instagram;
    $facebookUsuario = $facebook;
    echo "<script>alert('Informações atualizadas com sucesso!');</script>";
}

// Processar upload de imagem de perfil
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["foto_perfil"])) {
    $targetDir = "img/profiles/";
    $targetFile = $targetDir . basename($_FILES["foto_perfil"]["name"]);
    $uploadOk = 1;

    // Verificar se o diretório de destino existe e criar se necessário
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Verificar o tamanho e o formato do arquivo
    $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
    $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if ($_FILES["foto_perfil"]["size"] > 5000000) {
        echo "A imagem é muito grande.";
        $uploadOk = 0;
    }
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "Apenas imagens JPG, JPEG, PNG e GIF são permitidas.";
        $uploadOk = 0;
    }

    // Mover o arquivo e atualizar o banco de dados
    if ($uploadOk && move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $targetFile)) {
        $sql = "UPDATE usuarios SET foto_perfil = :fotoPerfil WHERE id = :usuarioId";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':fotoPerfil' => $targetFile, ':usuarioId' => $usuarioId]);
        
        // Atualiza o caminho da foto de perfil imediatamente para exibição
        $fotoPerfil = $targetFile;
        echo "<script>alert('Foto de perfil atualizada com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao enviar a imagem.');</script>";
    }
}

// Consulta SQL para buscar as visitas agendadas apenas do usuário logado
$sql_visitas = "SELECT v.id AS visita_id, v.id_imovel, v.id_corretor, v.data_visita, v.status, 
                i.endereco AS imovel, i.cidade, 
                c.nome AS corretor
                FROM visitas v
                JOIN imoveis i ON v.id_imovel = i.id
                JOIN corretores c ON v.id_corretor = c.id
                WHERE v.id_corretor = :usuarioId
                ORDER BY v.data_visita DESC
                LIMIT :inicio, :total_reg";

$stmt_visitas = $conn->prepare($sql_visitas);
$stmt_visitas->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
$stmt_visitas->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$stmt_visitas->bindParam(':total_reg', $total_reg, PDO::PARAM_INT);
$stmt_visitas->execute();

$total_records = $conn->query("SELECT COUNT(*) FROM visitas")->fetchColumn();
    $total_pages = ceil($total_records / $total_reg);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<section style="background-color: #eee;">
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

<body style="background-color: #eee;">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand mt-2 mt-lg-0" href="painel.php">FastImóveis</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="pesquisar.php">Pesquisar</a></li>
                    <?php if ($_SESSION["isAdmin"]) { ?>
                        <li class="nav-item"><a class="nav-link" href="gerenciar_usuarios.php">Usuários</a></li>
                        <li class="nav-item"><a class="nav-link" href="admin_solicitacoes.php">Solicitações</a></li>
                    <?php } ?>
                    <li class="nav-item"><a class="nav-link" href="favoritos.php">Favoritos</a></li>
                    <li class="nav-item"><a class="nav-link" href="agendar_visita.php">Visitas</a></li>
                    <li class="nav-item"><a class="nav-link" href="perfil.php">Perfil</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="gerar_pdf.php">Contrato</a>
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

    <!-- Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="perfil.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Atualizar Informações</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" value="<?= $telefoneUsuario ?>">
                        </div>
                        <div class="mb-3">
                            <label for="celular" class="form-label">Celular</label>
                            <input type="text" class="form-control" id="celular" name="celular" value="<?= $celularUsuario ?>">
                        </div>
                        <?php if ($isCorretor || $isAdmin) { ?>
                        <div class="mb-3">
                            <label for="imobiliaria" class="form-label">Imobiliária</label>
                            <input type="text" class="form-control" id="imobiliaria" name="imobiliaria" value="<?= $imobiliariaUsuario ?>">
                        </div>
                        <?php } ?>
                        <div class="mb-3">
                            <label for="github" class="form-label">Github</label>
                            <input type="text" class="form-control" id="github" name="github" value="<?= $githubUsuario ?>">
                        </div>
                        <div class="mb-3">
                            <label for="twitter" class="form-label">Twitter</label>
                            <input type="text" class="form-control" id="twitter" name="twitter" value="<?= $twitterUsuario ?>">
                        </div>
                        <div class="mb-3">
                            <label for="instagram" class="form-label">instagram</label>
                            <input type="text" class="form-control" id="instagram" name="instagram" value="<?= $instagramUsuario ?>">
                        </div>
                        <div class="mb-3">
                            <label for="facebook" class="form-label">Facebook</label>
                            <input type="text" class="form-control" id="facebook" name="facebook" value="<?= $facebookUsuario ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" name="update_info" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-body text-center">
            <!-- Exibir imagem de perfil -->
            <img src="<?= $fotoPerfil ?>" alt="Foto de perfil do usuário" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
              <h5 class="my-3"><?= $nomeUsuario ?></h5>
            <p class="text-muted mb-1">Full Stack Developer</p>
            <?php if ($isCorretor||$isAdmin) { ?>
            <p class="text-muted mb-4"><?= $imobiliariaUsuario ?></p>
            <?php } ?>
            <!-- Formulário para upload da imagem -->
            <form action="perfil.php" method="post" enctype="multipart/form-data">
                            <input type="file" name="foto_perfil" class="form-control mt-3 mb-3" accept=".jpg, .jpeg, .png, .gif" required>
                            <button type="submit" class="btn btn-primary">Atualizar Foto</button>
                        </form>
          </div>
        </div>
        <div class="card mb-4 mb-lg-0">
          <div class="card-body p-0">
            <ul class="list-group list-group-flush rounded-3">
              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                <i class="fab fa-github fa-lg text-body"></i>
                <p class="mb-0"><?= $githubUsuario ?></p>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                <i class="fab fa-twitter fa-lg" style="color: #55acee;"></i>
                <p class="mb-0">@<?= $twitterUsuario ?></p>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                <i class="fab fa-instagram fa-lg" style="color: #ac2bac;"></i>
                <p class="mb-0"><?= $instagramUsuario ?></p>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                <i class="fab fa-facebook-f fa-lg" style="color: #3b5998;"></i>
                <p class="mb-0"><?= $facebookUsuario ?></p>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Nome</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?= $nomeUsuario ?></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Email</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?= $emailUsuario ?></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Telefone</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?= $telefoneUsuario ?></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Celular</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?= $celularUsuario ?></p>
              </div>
            </div>
            <hr>
            <?php if ($isCorretor||$isAdmin) { ?>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0">Imobiliária</p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?= $imobiliariaUsuario ?></p>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
        <!-- Botão para abrir o modal -->
    <div class="container mt-5 text-center">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal">
            Atualizar Informações de Contato
        </button>
    </div>
        <div class="container mt-5">
          
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
          </div>
         </div>
        </div>  
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

    <!-- Incluir scripts JavaScript necessários (jQuery, Bootstrap JS) -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</section>
</html>
