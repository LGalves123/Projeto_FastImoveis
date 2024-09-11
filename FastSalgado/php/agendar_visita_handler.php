<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_imovel = $_POST['id_imovel'];
    $id_corretor = $_POST['id_corretor'];
    $data_visita = $_POST['data_visita'];
    $status = $_POST['status'];

    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verifica se estamos editando uma visita existente ou criando uma nova
        if (isset($_POST['visitaId']) && is_numeric($_POST['visitaId'])) {
            // Edição
            $visitaId = $_POST['visitaId'];
            $stmt = $conn->prepare("UPDATE visitas SET id_imovel = :id_imovel, id_corretor = :id_corretor, data_visita = :data_visita, status = :status WHERE id = :visitaId");
            $stmt->bindParam(':id_imovel', $id_imovel);
            $stmt->bindParam(':id_corretor', $id_corretor);
            $stmt->bindParam(':data_visita', $data_visita);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':visitaId', $visitaId);
            $stmt->execute();

            echo "Visita editada com sucesso!";
        } else {
            // Criação
            $stmt = $conn->prepare("INSERT INTO visitas (id_imovel, id_corretor, data_visita, status) VALUES (:id_imovel, :id_corretor, :data_visita, :status)");
            $stmt->bindParam(':id_imovel', $id_imovel);
            $stmt->bindParam(':id_corretor', $id_corretor);
            $stmt->bindParam(':data_visita', $data_visita);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            echo "Visita agendada com sucesso!";
        }
    } catch (PDOException $e) {
        echo "Erro ao agendar visita: " . $e->getMessage();
    }
}