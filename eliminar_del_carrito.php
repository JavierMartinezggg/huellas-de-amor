<?php
session_start();

if (isset($_GET["index"])) {
    $index = intval($_GET["index"]);

    if (isset($_SESSION["carrito"][$index])) {
        unset($_SESSION["carrito"][$index]);
        $_SESSION["carrito"] = array_values($_SESSION["carrito"]);
        echo "Producto eliminado";
        
        // Redirigir de vuelta
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }

    echo "Producto no encontrado";
    exit;
}

echo "Petición inválida";
?>