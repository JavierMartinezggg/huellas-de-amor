<?php
include_once("conexion.php");

// Función para mostrar productos
function mostrarProductos($resultado) {
    if ($resultado->num_rows > 0) {
        while ($producto = $resultado->fetch_assoc()) {
            echo '
            <div class="pop-card">
                <img src="' . htmlspecialchars($producto['imagen']) . '" 
                     alt="' . htmlspecialchars($producto['nombre']) . '" 
                     loading="lazy" width="300" height="200">
                <h3>' . htmlspecialchars($producto['nombre']) . '</h3>
                <p class="precio">$' . number_format($producto['precio'], 0, ',', '.') . '</p>
                <button class="btn-popular" data-add="' . $producto['id'] . '" 
                        aria-label="Agregar ' . htmlspecialchars($producto['nombre']) . ' al carrito">
                    <i class="fa-solid fa-cart-shopping"></i> Agregar
                </button>
            </div>';
        }
    } else {
        echo '<p>No hay productos disponibles.</p>';
    }
}

// Consultas
$sql_gatos = "SELECT * FROM productos WHERE categoria='Gatos' ORDER BY id DESC LIMIT 10";
$sql_perros = "SELECT * FROM productos WHERE categoria='Perros' ORDER BY id DESC LIMIT 10";


$resultado_gatos = $conn->query($sql_gatos);
$resultado_perros = $conn->query($sql_perros);
?>