<?php
session_start();
include("conexion.php");

header('Content-Type: application/json');

if (isset($_GET['marca'])) {
    $marca = $conn->real_escape_string($_GET['marca']);
    
    // Consulta para obtener productos por marca
    $sql = "SELECT * FROM productos WHERE marca = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $marca);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $productos = [];
    while($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos)
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No se especificó marca'
    ]);
}
?>