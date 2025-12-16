<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

// Acciones reales con BD
if (isset($_GET['marcar_leido'])) {
    $id = intval($_GET['marcar_leido']);
    $conn->query("UPDATE mensajes_contacto SET leido = TRUE WHERE id = $id");
    header("Location: mensajes_contacto.php");
    exit;
}

if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM mensajes_contacto WHERE id = $id");
    header("Location: mensajes_contacto.php");
    exit;
}

// Obtener mensajes REALES de la BD
$mensajes = $conn->query("SELECT * FROM mensajes_contacto ORDER BY fecha DESC");
$total_mensajes = $mensajes->num_rows;
$no_leidos = $conn->query("SELECT COUNT(*) as total FROM mensajes_contacto WHERE leido = FALSE")->fetch_assoc()['total'];
?>