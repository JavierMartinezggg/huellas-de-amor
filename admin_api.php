<?php
// admin_api.php
header('Content-Type: application/json');
require_once 'config/database.php';

// Solo para listar productos (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($productos);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Error al obtener productos']);
    }
}
?>