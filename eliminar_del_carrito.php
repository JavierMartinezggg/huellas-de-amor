<?php
session_start();

// CAMBIO: Ahora acepta GET en lugar de POST
if (isset($_GET["index"])) {
    $index = intval($_GET["index"]);

    if (isset($_SESSION["carrito"][$index])) {
        unset($_SESSION["carrito"][$index]);
        $_SESSION["carrito"] = array_values($_SESSION["carrito"]);
        echo "Producto eliminado";
        
        // CAMBIO: Redirigir de vuelta
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    echo "Producto no encontrado";
    exit;
}
echo "Petición inválida";
?>