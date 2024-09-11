<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fastimoveis";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM imoveis WHERE name LIKE '%$search%' OR other_column LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<div>" . $row["name"] . " - " . $row["other_column"] . "</div>";
        }
    } else {
        echo "Nenhum resultado encontrado.";
    }
}

$conn->close();
?>