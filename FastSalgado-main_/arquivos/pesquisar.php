<?php
// Conexão com o banco de dados
$dbHost = "localhost";
$dbName = "fastimoveis";
$dbUser = "root";
$dbPass = "";

// Defina o número de imóveis por página
$limite = 5;

// Determine a página atual
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $limite;


try {
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se há uma pesquisa por cidade
    $cidade = isset($_GET['cidade']) ? $_GET['cidade'] : '';

    // Consulta SQL para buscar todos os imóveis, ou filtrar por cidade se especificado
    if ($cidade) {
        $stmt = $conn->prepare("SELECT * FROM imoveis WHERE cidade LIKE :cidade");
        $stmt->bindValue(':cidade', "%$cidade%");
    } else {
        $stmt = $conn->query("SELECT * FROM imoveis");
    }
    $stmt->execute();

    // Consulta SQL para buscar imóveis com limite e offset
    if ($cidade) {
        $stmt = $conn->prepare("SELECT * FROM imoveis WHERE cidade LIKE :cidade LIMIT :limite OFFSET :offset");
        $stmt->bindValue(':cidade', "%$cidade%");
    } else {
        $stmt = $conn->prepare("SELECT * FROM imoveis LIMIT :limite OFFSET :offset");
    }

    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    // Calcule o total de páginas
    $totalImoveis = $conn->query("SELECT COUNT(*) FROM imoveis")->fetchColumn();
    $totalPaginas = ceil($totalImoveis / $limite);

    // Definir a variável para acompanhar o índice do item ativo
    $activeIndex = 0;

} catch(PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar - FastImóveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilização da Paginação */
        body {
            padding-top: 56px; /* Espaço para a navbar fixa */
        }
        .pagination {
            display: flex;
            justify-content: right;
            align-items: right;
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .pagination a {
            color: #fff;
            background-color: #333;
            padding: 10px 15px;
            margin: 0 5px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .pagination a:hover {
            background-color: hsl(47, 88%, 53%);
        }
        .pagination a.active {
            background-color: hsl(47, 88%, 63%);
            color: #000;
            font-weight: bold;
        }
        .pagination a.disabled {
            pointer-events: none;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <!-- Verificar esta Navbar, para que ocupe toda a largura horizontal da tela -->
    <header>
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">FastImóveis</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item">
                            <a class="nav-link" href="destaques.php">Destaques</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pesquisar.php">Pesquisar</a>
                        </li>
                    </ul>
                    <form class="d-flex" role="search" action="pesquisar.php" method="get">
                        <input class="form-control me-2" type="search" name="cidade" placeholder="Pesquisar imóveis" aria-label="Pesquisar">
                        <button class="btn btn-outline-success" type="submit">Buscar</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>


    <!-- Slider -->
    <div class="slider">
        <div class="list">
            <?php
            // Loop através dos resultados
            $index = 0;
            while ($imovel = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $activeClass = $index === $activeIndex ? "active" : "";
                echo "
                <div class=\"item $activeClass\" data-index=\"$index\">
                    <img src=\"../php/{$imovel['foto']}\">
                    <div class=\"content\">
                        <p>{$imovel['categoria']}</p>
                        <h2>{$imovel['status']}</h2>
                        <p>{$imovel['endereco']} {$imovel['cidade']}</p>
                        <p>{$imovel['descricao']}</p>
                    </div>
                </div>
                ";
                $index++;
            }
            ?>
        </div>
        <div class="arrows">
            <button id="prev"><</button>
            <button id="next">></button>

            <!-- Paginação -->
    <div class="pagination">
        <?php if ($paginaAtual > 1): ?>
            <a href="?pagina=<?= $paginaAtual - 1 ?>&cidade=<?= $cidade ?>">Anterior</a>
        <?php else: ?>
            <a href="#" class="disabled">Anterior</a>
        <?php endif; ?>

        <?php /* for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?= $i ?>&cidade=<?= $cidade ?>" class="<?= $i == $paginaAtual ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; */ ?>  <!-- este bloco com laço for é para a apresentaçao da quantidade de páginas, se quiser que apareça novamente, apenas tirar o comentário--> 

        <?php if ($paginaAtual < $totalPaginas): ?>
            <a href="?pagina=<?= $paginaAtual + 1 ?>&cidade=<?= $cidade ?>">Próxima</a>
        <?php else: ?>
            <a href="#" class="disabled">Próxima</a>
        <?php endif; ?>
    </div>

        </div>
        <div class="thumbnail">
            <?php
            // Volte para o início para reutilizar o resultado da consulta
            $stmt->execute();

            // Loop através dos resultados para mostrar as miniaturas
            $index = 0;
            while ($imovel = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $activeClass = ($cidade && stripos($imovel['cidade'], $cidade) !== false) ? "active" : "";
                echo "
                <div class=\"item $activeClass\" data-index=\"$index\">
                    <img src=\"../php/{$imovel['foto']}\">
                    <div class=\"content\">
                        <button class=\"btn login-btn\" onclick=\"window.location.href='property.php?id={$imovel['id']}'\">Mais</button>
                    </div>
                </div>
                ";
                $index++;
            }
            ?>
        </div>
    </div>

    <script src="index.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


</body>
</html>
