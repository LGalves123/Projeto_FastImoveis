<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado da Busca - FastImóveis</title>
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
        }
        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .image-cell {
            max-width: 100px;
            max-height: 100px;
        }
        .status {
            text-transform: capitalize;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Resultados da Busca</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Endereço</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Nome do Vendedor</th>
            <th>Telefone do Vendedor</th>
            <th>Email do Vendedor</th>
            <th>Foto</th>
            <th>Status</th>
        </tr>
        <?php
        // Verificar se as variáveis da URL foram definidas
        if (isset($_GET['city']) && isset($_GET['property_type'])) {
            $city = $_GET['city'];
            $propertyType = $_GET['property_type'];
            
            // Conectar ao banco de dados e selecionar os imóveis correspondentes
            try {
                $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8mb4", "root", "");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT * FROM imoveis WHERE cidade = :city AND categoria = :propertyType";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':city', $city);
                $stmt->bindParam(':propertyType', $propertyType);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['endereco'] . "</td>";
                    echo "<td>" . $row['categoria'] . "</td>";
                    echo "<td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>";
                    echo "<td>" . $row['nome_vendedor'] . "</td>";
                    echo "<td>" . $row['telefone_vendedor'] . "</td>";
                    echo "<td>" . $row['email_vendedor'] . "</td>";
                    echo "<td><img src='" . $row['foto'] . "' alt='Foto do Imóvel' class='image-cell'></td>";
                    echo "<td class='status'>" . $row['status'] . "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "Erro: " . $e->getMessage();
            }
        } else {
            echo "<tr><td colspan='9'>Nenhum resultado encontrado.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
