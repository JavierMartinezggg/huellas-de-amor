<?php
// Datos de conexión
$servidor = "localhost";
$usuario = "root";   // por defecto en XAMPP
$password = "";      // por defecto está vacío
$base_datos = "huellas_db";

// Crear conexión
$conn = new mysqli($servidor, $usuario, $password, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
} else {
    
}
?>
