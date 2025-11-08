<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

// PROCESAR ELIMINACIÓN - FUNCIONAL  
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM pedidos WHERE id = $id");
    header("Location: pedidos.php?success=Pedido eliminado");
    exit;
}

// OBTENER PEDIDOS
$sql = "SELECT * FROM pedidos ORDER BY fecha DESC";
$pedidos = $conn->query($sql);

// ESTADÍSTICAS SIMPLIFICADAS (sin columna estado)
$total_pedidos = $pedidos->num_rows;
$ingresos_totales = $conn->query("SELECT SUM(total) as total FROM pedidos")->fetch_assoc()['total'] ?? 0;
$pedidos_hoy = $conn->query("SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha) = CURDATE()")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Pedidos - Panel Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #2c3e50;
      --secondary: #3498db;
      --success: #2ecc71;
      --danger: #e74c3c;
      --warning: #f39c12;
      --light: #ecf0f1;
      --dark: #34495e;
      --sidebar-width: 250px;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background-color: #f5f7fa;
      color: #333;
      display: flex;
      min-height: 100vh;
    }
    
    .sidebar {
      width: var(--sidebar-width);
      background: var(--primary);
      color: white;
      position: fixed;
      height: 100vh;
      z-index: 1000;
    }
    
    .sidebar-header {
      padding: 20px;
      background: var(--dark);
      text-align: center;
    }
    
    .sidebar-menu {
      padding: 20px 0;
    }
    
    .sidebar-menu ul {
      list-style: none;
      padding: 0;
    }
    
    .sidebar-menu li {
      padding: 0;
      margin: 5px 0;
    }
    
    .sidebar-menu a {
      padding: 12px 20px;
      display: block;
      color: #ddd;
      text-decoration: none;
      transition: all 0.3s;
      font-size: 16px;
    }
    
    .sidebar-menu a:hover, .sidebar-menu a.active {
      background: var(--dark);
      color: white;
      border-left: 4px solid var(--secondary);
    }
    
    .sidebar-menu i {
      margin-right: 10px;
      width: 20px;
      text-align: center;
    }
    
    .main-content {
      flex: 1;
      margin-left: var(--sidebar-width);
      padding: 20px;
    }
    
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }
    
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border-left: 4px solid var(--primary);
    }
    
    .card-title {
      font-size: 13px;
      color: #6c757d;
      margin-bottom: 5px;
      font-weight: 600;
    }
    
    .card-value {
      font-size: 22px;
      font-weight: 700;
      color: var(--primary);
    }
    
    .table-container {
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      margin-bottom: 20px;
    }
    
    .table-header {
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #eee;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
    }
    
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    
    th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #495057;
    }
    
    tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .action-btn {
      padding: 6px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 12px;
      margin-right: 5px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      transition: all 0.2s;
    }
    
    .btn-eliminar {
      background: #fce8e6;
      color: #d93025;
      border: 1px solid #fadbd8;
    }
    
    .btn-eliminar:hover {
      background: #fadbd8;
    }
    
    .btn-detalles {
      background: #e6f4ea;
      color: #138a54;
      border: 1px solid #cce7d7;
    }
    
    .btn-detalles:hover {
      background: #d4edda;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-header">
    <h3>Panel Admin</h3>
  </div>
  <nav class="sidebar-menu">
    <ul>
      <li><a href="admin_panel.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
      <li><a href="pedidos.php" class="active"><i class="fas fa-shopping-cart"></i> <span>Pedidos</span></a></li>
      <li><a href="productos_admin.php"><i class="fas fa-box"></i> <span>Productos</span></a></li>
      <li><a href="clientes.php"><i class="fas fa-users"></i> <span>Clientes</span></a></li>
    </ul>
  </nav>
</div>

<div class="main-content">
  <div class="header">
    <div>
      <h1>Gestión de Pedidos</h1>
      <p>Administra los pedidos de tu tienda</p>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
      <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
  <?php endif; ?>

  <!-- ESTADÍSTICAS -->
  <div class="card-grid">
    <div class="card">
      <div class="card-title">TOTAL PEDIDOS</div>
      <div class="card-value"><?= $total_pedidos ?></div>
    </div>
    <div class="card">
      <div class="card-title">PEDIDOS HOY</div>
      <div class="card-value"><?= $pedidos_hoy ?></div>
    </div>
    <div class="card">
      <div class="card-title">INGRESOS TOTALES</div>
      <div class="card-value">$<?= number_format($ingresos_totales, 0, ',', '.') ?></div>
    </div>
  </div>

  <!-- TABLA DE PEDIDOS -->
  <div class="table-container">
    <div class="table-header">
      <h2>Lista de Pedidos (<?= $total_pedidos ?>)</h2>
    </div>
    
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Productos</th>
          <th>Total</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($pedido = $pedidos->fetch_assoc()): ?>
        <tr>
          <td><strong>#<?= $pedido['id'] ?></strong></td>
          <td><?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></td>
          <td>
            <div style="max-height: 80px; overflow-y: auto;">
              <?= nl2br(htmlspecialchars($pedido['productos'])) ?>
            </div>
          </td>
          <td style="font-weight: bold; color: #e85c00;">
            $<?= number_format($pedido['total'], 0, ',', '.') ?>
          </td>
          <td>
            <div style="display: flex; gap: 5px;">
              <button class="action-btn btn-detalles" onclick="verDetalles(<?= $pedido['id'] ?>)">
                <i class="fas fa-eye"></i> Ver
              </button>
              <a href="pedidos.php?eliminar=<?= $pedido['id'] ?>" 
                 class="action-btn btn-eliminar"
                 onclick="return confirm('¿Estás seguro de eliminar este pedido?')">
                <i class="fas fa-trash"></i> Eliminar
              </a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function verDetalles(pedidoId) {
  alert('Detalles del Pedido #' + pedidoId);
}
</script>

</body>
</html>