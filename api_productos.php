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
        $productos[] = [
            "id" => $fila["id"],
            "nombre" => $fila["nombre"],
            "descripcion" => $fila["descripcion"],
            "precio" => $fila["precio"],
            "descuento" => $fila["descuento"],
            "imagen" => "images/" . $fila["imagen"] // 👈 Aquí está la clave
        ];
    }
}

// Devolver como JSON (para usarlo en JS)
echo json_encode($productos);
?>
