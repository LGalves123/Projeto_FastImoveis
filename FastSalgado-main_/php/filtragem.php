<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastImóveis</title>    
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }
        .dropdown {
            margin-bottom: 20px;
        }
        .dropdown select {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .button {
            padding: 12px 24px;
            font-size: 18px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
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

<a href="painel.php">Painel</a>
<div class="container">
    <h1>FastImóveis</h1>
    <div class="dropdown">
        <select id="city" name="city">
            <option value="" selected disabled>Selecione sua cidade</option>
            <!-- PHP para buscar as cidades no banco de dados -->
            <?php
            try {
                // Conectar ao banco de dados
                $conn = new PDO("mysql:host=localhost;dbname=fastimoveis", "root", "");
                // Configurar o PDO para lançar exceções em caso de erro
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Query para buscar as cidades distintas no banco de dados
                $sql = "SELECT DISTINCT cidade FROM imoveis";

                // Preparar e executar a consulta
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                // Exibir as opções do dropdown
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . $row['cidade'] . "'>" . $row['cidade'] . "</option>";
                }
            } catch (PDOException $e) {
                echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
            }
            ?>
        </select>
    </div>
    <div class="dropdown">
        <select id="property_type" name="property_type">
            <option value="" selected disabled>Escolha o tipo de imóvel</option>
            <option value="casa">Casa</option>
            <option value="apartamento">Apartamento</option>
            <option value="kitnet">Kitnet</option>
            <option value="sobrado">Sobrado</option>
            <option value="mansao">Mansão</option>
        </select>
    </div>
    <button class="button" onclick="filterProperties()">Buscar</button>
</div>

<script>
    function filterProperties() {
        var city = document.getElementById('city').value;
        var propertyType = document.getElementById('property_type').value;
        // Redirecionar para a página com os resultados da busca
        if (city && propertyType) {
            window.location.href = 'result.php?city=' + city + '&property_type=' + propertyType;
        } else {
            alert('Por favor, selecione uma cidade e um tipo de imóvel.');
        }
    }
</script>

</body>
</html>
