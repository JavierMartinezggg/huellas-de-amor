<?php
include("conexion.php");

echo "<h3>Mejorando Base de Datos...</h3>";

// Agregar columna estado a pedidos si no existe
$sqls = [
    "ALTER TABLE pedidos ADD COLUMN IF NOT EXISTS estado ENUM('pendiente', 'confirmado', 'en_camino', 'entregado', 'cancelado') DEFAULT 'pendiente'",
    "ALTER TABLE pedidos ADD COLUMN IF NOT EXISTS usuario_id INT NULL",
    "ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS activo BOOLEAN DEFAULT TRUE",
    "ALTER TABLE productos ADD COLUMN IF NOT EXISTS stock INT DEFAULT 0",
    "ALTER TABLE productos ADD COLUMN IF NOT EXISTS destacado BOOLEAN DEFAULT FALSE"
];

foreach ($sqls as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "✅ Éxito: " . substr($sql, 0, 50) . "...<br>";
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
}

echo "<h3>Base de datos mejorada correctamente!</h3>";
echo '<a href="admin_panel.php">Ir al Panel de Administración</a>';
?>