<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $visitaId = $_POST['visitaId'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("DELETE FROM visitas WHERE id_imovel = :visitaId");
        $stmt->bindParam(':visitaId', $visitaId);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Visita excluída com sucesso!"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Erro ao excluir visita: " . $e->getMessage()]);
    }
}
?>
