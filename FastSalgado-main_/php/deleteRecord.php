<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = new PDO("mysql:host=localhost;dbname=fastimoveis;charset=utf8", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Recebe o ID da visita
        if (isset($_POST['visitaId']) && is_numeric($_POST['visitaId'])) {
            $visitaId = (int)$_POST['visitaId'];

            // Query para deletar a visita
            $stmt = $conn->prepare("DELETE FROM visitas WHERE id = :id");
            $stmt->bindParam(':id', $visitaId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Visita excluída com sucesso.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir a visita.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
}
?>