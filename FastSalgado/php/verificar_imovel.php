<?php
if (isset($_GET['id_imovel'])) {
    $id_imovel = $_GET['id_imovel'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM imoveis WHERE id = :id_imovel");
        $stmt->bindParam(':id_imovel', $id_imovel, PDO::PARAM_INT);
        $stmt->execute();

        $exists = $stmt->fetchColumn() > 0;

        echo json_encode(['exists' => $exists]);
    } catch (PDOException $e) {
        echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['exists' => false]);
}
