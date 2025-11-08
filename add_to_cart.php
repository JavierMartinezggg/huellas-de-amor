<?php
session_start();
include("conexion.php");

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["producto_id"])) {
    $id = intval($_POST["producto_id"]);
    
    // Validar ID
    if ($id <= 0) {
        echo "Error: ID de producto inválido";
        exit;
    }

    try {
        // Buscar producto con stock disponible
        $sql = "SELECT id, nombre, precio, imagen, stock FROM productos WHERE id = ? AND stock > 0";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $producto = $resultado->fetch_assoc();
            
            // Inicializar carrito si no existe
            if (!isset($_SESSION["carrito"])) {
                $_SESSION["carrito"] = [];
            }

            // Buscar si el producto ya está en el carrito
            $producto_encontrado = false;
            foreach ($_SESSION["carrito"] as &$item) {
                if ($item["id"] == $producto["id"]) {
                    // Si ya existe, aumentar cantidad (máximo según stock)
                    $cantidad_maxima = min($producto["stock"], 10); // Límite de 10 por producto
                    if ($item["cantidad"] < $cantidad_maxima) {
                        $item["cantidad"]++;
                        $producto_encontrado = true;
                        echo "Cantidad aumentada: " . $item["cantidad"];
                    } else {
                        echo "Límite máximo alcanzado (Stock: " . $producto["stock"] . ")";
                    }
                    break;
                }
            }

            // Si no existe, agregarlo al carrito
            if (!$producto_encontrado) {
                $_SESSION["carrito"][] = [
                    "id" => $producto["id"],
                    "nombre" => $producto["nombre"],
                    "precio" => $producto["precio"],
                    "imagen" => $producto["imagen"],
                    "cantidad" => 1,
                    "stock_disponible" => $producto["stock"]
                ];
                echo "Producto añadido al carrito";
            }

        } else {
            echo "Producto no disponible o sin stock";
        }

        $stmt->close();
        
    } catch (Exception $e) {
        echo "Error al procesar la solicitud";
    }
    
    $conn->close();
} else {
    echo "Solicitud inválida";
}
?>