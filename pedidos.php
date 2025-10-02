<?php
session_start();
include("conexion.php");

// Verifica si el usuario está logueado como admin
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

// Consulta pedidos
$sql = "SELECT * FROM pedidos ORDER BY fecha DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pedidos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">🛒 Lista de pedidos</h2>
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Productos</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $resultado->fetch_assoc()) { ?>
        <tr>
          <td><?= $row["id"] ?></td>
          <td><?= $row["fecha"] ?></td>
          <td><?= nl2br($row["productos"]) ?></td>
          <td>$<?= number_format($row["total"]) ?></td>
          <td><?= $row["estado"] ?></td>
          <td><a href="ver_pedido.php?id=<?= $row["id"] ?>" class="btn btn-primary btn-sm">👁️ Ver</a></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>
