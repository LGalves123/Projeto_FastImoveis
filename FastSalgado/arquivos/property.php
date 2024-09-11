<?php
// Conexão com o banco de dados
$dbHost = "localhost";
$dbName = "fastimoveis";
$dbUser = "root";
$dbPass = "";

// Defina o número de imóveis por página
$limite = 5;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $limite;

try {
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM imoveis LIMIT :limite OFFSET :offset");
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $imoveis = $stmt->fetchAll();

} catch(PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Imóvel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
    body {
        background-color: #ffffff;
        color: #333;
        font-family: Arial, sans-serif;
    }
    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }
    .card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px; /* Ajuste o espaçamento entre os cartões */
        justify-content: center; /* Centraliza os cartões */
    }
    .property-card, .image-card {
        background-color: #f8f9fa;
        border-radius: 15px;
        padding: 10px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        flex: 1;
        min-width: 250px; /* Ajuste a largura mínima dos cartões */
    }
    .property-card h1, .property-card p, .image-card p {
        color: #333;
    }
    .property-info, .property-image {
        margin: 10px 0;
        word-wrap: break-word;
    }
    .property-info p, .image-card p {
        margin: 15px 0;
        text-align: justify;
    }
    .property-info strong, .image-card strong {
        display: block;
        margin-bottom: 5px;
    }
    .property-image img {
        max-width: 100%;
        border-radius: 20px;
    }

.property-info, .property-image {
    margin: 5px 0; /* Reduzido de 10px */
}

    .navbar {
        margin-bottom: 20px;
    }
    #map {
        height: 500px;
        width: 100%; /* Ajusta a largura do mapa */
        margin-top: 20px;
    }
    .carousel {
        margin-bottom: 10px;
    }
    .carousel-item img {
        max-width: 80%;
        height: auto;
    }
    .carousel-control-prev, .carousel-control-next {
        width: 5%;
    }
    .icon {
        width: 40px;
        height: auto;
        display: inline-block;
    }
    .card-container .property-card:nth-child(3), 
    .card-container .property-card:nth-child(4),
    .card-container .property-card:nth-child(5),
    .card-container .image-card:nth-child(3), 
    .card-container .image-card:nth-child(4),
    .card-container .image-card:nth-child(5) {
        flex: 1 1 100%; /* Faz com que os cartões 3, 4 e 5 ocupem 100% da largura disponível e fiquem um abaixo do outro */
    }
    .card-container .property-card:nth-child(1), 
    .card-container .property-card:nth-child(2),
    .card-container .image-card:nth-child(1), 
    .card-container .image-card:nth-child(2) {
        flex: 1 1 calc(50% - 20px); /* Define os cartões 1 e 2 para ocupar 50% da largura disponível com espaçamento */
    }
    @media (max-width: 768px) {
        .card-container .property-card, 
        .card-container .image-card {
            flex: 1 1 100%; /* Faz com que os cartões ocupem 100% da largura em telas menores */
        }
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Início</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="sobre.php">Sobre</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contato.php">Contato</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php
        if (isset($_GET['id'])) {
            $imovel_id = $_GET['id'];

            try {
                $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $conn->prepare("SELECT * FROM imoveis WHERE id = ?");
                $stmt->execute([$imovel_id]);

                if ($stmt->rowCount() > 0) {
                    $imovel = $stmt->fetch(PDO::FETCH_ASSOC);

                    echo "<div class='card-container'>";
                    echo "<div class='property-card'>";
                    echo "<h1>Detalhes do Imóvel</h1>";
                    echo "<div class='property-info'>";
                    echo "<p>{$imovel['descricao']}</p>";
                    echo "<div class='icon-container'>";
                    echo "<div class='icon-item'><img src='../cama.png' alt='Cama' class='icon'><h6>Dormitórios</h6></div>";
                    echo "<div class='icon-item'><img src='../garagem.png' alt='Garagem' class='icon'><h6>Garagens</h6></div>";
                    echo "<div class='icon-item'><img src='../planta-da-casa.png' alt='Planta' class='icon'><h6>Plantas</h6></div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";

                    // Exibir o cartão com a primeira imagem
                    $imagePath = "../php/{$imovel['foto']}";
                    echo "<div class='image-card'>";
                    if (file_exists($imagePath)) {
                        echo "<div class='property-image'>";
                        echo "<img src='{$imagePath}' alt='{$imovel['descricao']}'>";
                        echo "</div>";
                    } else {
                        echo "<p>Imagem não encontrada.</p>";
                    }
                    echo "</div>";

                    // Carrossel de Dormitórios
                    echo "<div class='property-card'>";
                    echo "<h3>Dormitórios</h3>";

                    $stmtDormitorios = $conn->prepare("SELECT imagem_url FROM dormitorios_imagens WHERE imovel_id = :imovel_id");
                    $stmtDormitorios->bindValue(':imovel_id', $imovel_id, PDO::PARAM_INT);
                    $stmtDormitorios->execute();
                    $imagensDormitorios = $stmtDormitorios->fetchAll(PDO::FETCH_COLUMN);

                    echo "<div id='dormitorioCarousel{$imovel_id}' class='carousel slide' data-ride='carousel'>";
                    echo "<div class='carousel-inner'>";
                    foreach ($imagensDormitorios as $index => $imagem) {
                        $activeClass = $index === 0 ? 'active' : '';
                        echo "<div class='carousel-item $activeClass'>";
                        echo "<img src='$imagem' class='d-block w-100' alt='Imagem de dormitório'>";
                        echo "</div>";
                    }
                    echo "</div>";
                    echo "<a class='carousel-control-prev' href='#dormitorioCarousel{$imovel_id}' role='button' data-slide='prev'>";
                    echo "<span class='carousel-control-prev-icon' aria-hidden='true'></span>";
                    echo "</a>";
                    echo "<a class='carousel-control-next' href='#dormitorioCarousel{$imovel_id}' role='button' data-slide='next'>";
                    echo "<span class='carousel-control-next-icon' aria-hidden='true'></span>";
                    echo "</a>";
                    echo "</div>";
                    echo "</div>";

                    // Carrossel de Garagens
                    echo "<div class='property-card'>";
                    echo "<h3>Garagens</h3>";

                    $stmtGaragens = $conn->prepare("SELECT imagem_url FROM garagens_imagens WHERE imovel_id = :imovel_id");
                    $stmtGaragens->bindValue(':imovel_id', $imovel_id, PDO::PARAM_INT);
                    $stmtGaragens->execute();
                    $imagensGaragens = $stmtGaragens->fetchAll(PDO::FETCH_COLUMN);

                    echo "<div id='garagemCarousel{$imovel_id}' class='carousel slide' data-ride='carousel'>";
                    echo "<div class='carousel-inner'>";
                    foreach ($imagensGaragens as $index => $imagem) {
                        $activeClass = $index === 0 ? 'active' : '';
                        echo "<div class='carousel-item $activeClass'>";
                        echo "<img src='$imagem' class='d-block w-100' alt='Imagem de garagem'>";
                        echo "</div>";
                    }
                    echo "</div>";
                    echo "<a class='carousel-control-prev' href='#garagemCarousel{$imovel_id}' role='button' data-slide='prev'>";
                    echo "<span class='carousel-control-prev-icon' aria-hidden='true'></span>";
                    echo "</a>";
                    echo "<a class='carousel-control-next' href='#garagemCarousel{$imovel_id}' role='button' data-slide='next'>";
                    echo "<span class='carousel-control-next-icon' aria-hidden='true'></span>";
                    echo "</a>";
                    echo "</div>";
                    echo "</div>";

                    // Carrossel de Plantas
                    echo "<div class='property-card'>";
                    echo "<h3>Plantas</h3>";

                    $stmtPlantas = $conn->prepare("SELECT imagem_url FROM plantas_imagens WHERE imovel_id = :imovel_id");
                    $stmtPlantas->bindValue(':imovel_id', $imovel_id, PDO::PARAM_INT);
                    $stmtPlantas->execute();
                    $imagensPlantas = $stmtPlantas->fetchAll(PDO::FETCH_COLUMN);

                    echo "<div id='plantaCarousel{$imovel_id}' class='carousel slide' data-ride='carousel'>";
                    echo "<div class='carousel-inner'>";
                    foreach ($imagensPlantas as $index => $imagem) {
                        $activeClass = $index === 0 ? 'active' : '';
                        echo "<div class='carousel-item $activeClass'>";
                        echo "<img src='$imagem' class='d-block w-100' alt='Imagem de planta'>";
                        echo "</div>";
                    }
                    echo "</div>";
                    echo "<a class='carousel-control-prev' href='#plantaCarousel{$imovel_id}' role='button' data-slide='prev'>";
                    echo "<span class='carousel-control-prev-icon' aria-hidden='true'></span>";
                    echo "</a>";
                    echo "<a class='carousel-control-next' href='#plantaCarousel{$imovel_id}' role='button' data-slide='next'>";
                    echo "<span class='carousel-control-next-icon' aria-hidden='true'></span>";
                    echo "</a>";
                    echo "</div>";
                    echo "</div>";

                    // Adiciona o mapa com Leaflet
                    if (!empty($imovel['latitude']) && !empty($imovel['longitude'])) {
                        $latitude = htmlspecialchars($imovel['latitude']);
                        $longitude = htmlspecialchars($imovel['longitude']);
                        echo "<div id='map'></div>";
                        echo "<script>
                            var map = L.map('map').setView([$latitude, $longitude], 15);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
                            }).addTo(map);

                            L.marker([$latitude, $longitude]).addTo(map)
                                .bindPopup('Localização do Imóvel')
                                .openPopup();
                        </script>";
                    } else {
                        echo "<p>Coordenadas não disponíveis para este imóvel.</p>";
                    }

                } else {
                    echo "<p>Imóvel não encontrado.</p>";
                }
            } catch (PDOException $e) {
                echo "Erro ao se conectar com o banco de dados: " . $e->getMessage();
            }
        } else {
            echo "<p>ID do imóvel não especificado.</p>";
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
