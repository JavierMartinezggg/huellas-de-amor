<?php
require "conexion.php";

// Consultar productos
$sql = "SELECT id, nombre, descripcion, precio, imagen FROM productos ORDER BY id ASC";
$resultado = $conn->query($sql);

$productos = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {

        // Limpiar y asegurar la ruta correcta
        $nombreArchivo = basename($fila['imagen']); // extrae solo el nombre del archivo
        $rutaFinal = "images/" . $nombreArchivo;

        $productos[] = [
            "id" => $fila["id"],
            "nombre" => $fila["nombre"],
            "descripcion" => $fila["descripcion"],
            "precio" => $fila["precio"],
            "imagen" => $rutaFinal
        ];
    }
}

// Enviar como JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($productos, JSON_UNESCAPED_UNICODE);
?>
