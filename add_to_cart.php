<?php
session_start(); // Activamos la sesión para guardar datos del usuario

include("conexion.php"); // Conectamos con la base de datos

// Verificamos si se recibió el ID del producto por POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["producto_id"])) {
    $id = intval($_POST["producto_id"]); // Convertimos el ID a número

    // Buscamos el producto en la base de datos
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql); // Preparamos la consulta
    $stmt->bind_param("i", $id);   // Enviamos el ID
    $stmt->execute();              // Ejecutamos
    $resultado = $stmt->get_result(); // Obtenemos el resultado

    // Si el producto existe
    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc(); // Guardamos el producto

        // Si el carrito no existe, lo creamos
        if (!isset($_SESSION["carrito"])) {
            $_SESSION["carrito"] = [];
        }

        // Agregamos el producto al carrito
        $_SESSION["carrito"][] = [
            "id" => $producto["id"],
            "nombre" => $producto["nombre"],
            "precio" => $producto["precio"],
            "imagen" => $producto["imagen"]
        ];

        echo "Producto añadido";
    } else {
        echo "Producto no encontrado";
    }
}
?>
