<?php
require "conexion.php";

// Traer productos con descuento mayor a 0
$sql = "SELECT id, nombre, descripcion, precio, descuento, imagen 
        FROM productos 
        WHERE descuento > 0 
        ORDER BY creado_en DESC 
        LIMIT 6";

$resultado = $conn->query($sql);

$productos = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
}

// Devolver como JSON (para usarlo en JS)
echo json_encode($productos);
?>
