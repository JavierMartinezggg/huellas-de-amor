<?php
session_start();
include("conexion.php");

header('Content-Type: application/json');

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $termino = $conn->real_escape_string($_GET['q']);
    
    $sql = "SELECT * FROM productos 
            WHERE nombre LIKE ? 
               OR descripcion LIKE ? 
               OR categoria LIKE ?
            ORDER BY 
                CASE 
                    WHEN nombre LIKE ? THEN 1
                    WHEN descripcion LIKE ? THEN 2
                    ELSE 3
                END,
                nombre ASC
            LIMIT 20";
    
    $param = "%$termino%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $param, $param, $param, $param, $param);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $productos = [];
    while($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
    
    echo json_encode([
        'success' => true,
        'productos' => $productos,
        'total' => count($productos),
        'termino' => $termino
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No se especificó término de búsqueda'
    ]);
}
?>
