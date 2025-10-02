<?php
session_start();
include("conexion.php"); // Asegúrate de tener tu conexión aquí

if (!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])) {
    echo "Carrito vacío";
    exit;
}

$productos = $_SESSION["carrito"];
$total = 0;
$lista = [];

foreach ($productos as $p) {
    $total += $p["precio"];
    $lista[] = $p["nombre"] . " ($" . number_format($p["precio"], 0, ',', '.') . ")";
}

$productos_texto = implode(", ", $lista);
$fecha = date("Y-m-d H:i:s");

$sql = "INSERT INTO pedidos (fecha, productos, total) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssd", $fecha, $productos_texto, $total);
$stmt->execute();

$_SESSION["carrito"] = []; // Vacía el carrito

echo "Pedido registrado con éxito";
