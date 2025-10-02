<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

// Obtener estadísticas para el dashboard
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM pedidos");
$stats['total_pedidos'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT SUM(total) as total FROM pedidos");
$stats['ingresos_totales'] = $result->fetch_assoc()['total'] ?? 0;

$result = $conn->query("SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha) = CURDATE()");
$stats['pedidos_hoy'] = $result->fetch_assoc()['total'];

// Obtener pedidos
$sql = "SELECT * FROM pedidos ORDER BY fecha DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración - Sistema de Pedidos</title>
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
    
    /* Sidebar */
    .sidebar {
      width: var(--sidebar-width);
      background: var(--primary);
      color: white;
      position: fixed;
      height: 100vh;
      transition: all 0.3s ease;
      z-index: 1000;
    }
    
    .sidebar-header {
      padding: 20px;
      background: var(--dark);
      text-align: center;
    }
    
    .sidebar-header h3 {
      margin-bottom: 0;
      font-weight: 600;
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
    
    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: var(--sidebar-width);
      padding: 20px;
      transition: all 0.3s;
    }
    
    /* Header */
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
    
    .user-info {
      display: flex;
      align-items: center;
    }
    
    .user-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
    }
    
    /* Cards */
    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }
    
    .card {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
    }
    
    .card-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
      font-size: 20px;
    }
    
    .card-title {
      font-size: 14px;
      color: #777;
      margin-bottom: 5px;
    }
    
    .card-value {
      font-size: 24px;
      font-weight: 700;
    }
    
    .card-trend {
      margin-top: 10px;
      font-size: 13px;
      display: flex;
      align-items: center;
    }
    
    .card-trend.up {
      color: var(--success);
    }
    
    .card-trend.down {
      color: var(--danger);
    }
    
    /* Table */
    .table-container {
      background: white;
      border-radius: 8px;
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
    
    .table-header h2 {
      margin: 0;
      font-size: 18px;
    }
    
    .search-box {
      position: relative;
    }
    
    .search-box input {
      padding: 8px 15px 8px 35px;
      border: 1px solid #ddd;
      border-radius: 4px;
      width: 250px;
    }
    
    .search-box i {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #777;
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
    
    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }
    
    .status.completed {
      background: #e6f4ea;
      color: #138a54;
    }
    
    .status.pending {
      background: #fef7e0;
      color: #b3871f;
    }
    
    .status.cancelled {
      background: #fce8e6;
      color: #d93025;
    }
    
    .action-btn {
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
      margin-right: 5px;
    }
    
    .btn-view {
      background: #e8f0fe;
      color: #1a73e8;
    }
    
    .btn-edit {
      background: #e6f4ea;
      color: #138a54;
    }
    
    .btn-delete {
      background: #fce8e6;
      color: #d93025;
    }
    
    /* Footer */
    .footer {
      text-align: center;
      padding: 20px;
      color: #6c757d;
      font-size: 14px;
      margin-top: 40px;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
      .sidebar {
        width: 70px;
      }
      
      .sidebar .sidebar-header h3,
      .sidebar .sidebar-menu a span {
        display: none;
      }
      
      .sidebar .sidebar-menu i {
        margin-right: 0;
        font-size: 20px;
      }
      
      .main-content {
        margin-left: 70px;
      }
    }
    
    @media (max-width: 768px) {
      .card-grid {
        grid-template-columns: 1fr;
      }
      
      .search-box input {
        width: 150px;
      }
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
  <li><a href="admin_panel.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
  <li><a href="pedidos.php"><i class="fas fa-shopping-cart"></i> <span>Pedidos</span></a></li>
  <li><a href="productos_admin.php"><i class="fas fa-box"></i> <span>Productos</span></a></li>
  <li><a href="clientes.php"><i class="fas fa-users"></i> <span>Clientes</span></a></li>
  <li><a href="reportes.php"><i class="fas fa-chart-bar"></i> <span>Reportes</span></a></li>
  <li><a href="configuracion.php"><i class="fas fa-cog"></i> <span>Configuración</span></a></li>
</ul>
  </nav>
</div>

<div class="main-content">
  <div class="header">
    <div>
      <h1>Panel de Administración</h1>
    </div>
    <div class="user-info">
      <img src="https://ui-avatars.com/api/?name=Administrador&background=3498db&color=fff" alt="Usuario">
      <div>
        <div>Administrador</div>
        <div style="font-size: 12px; color: #777;">Super Admin</div>
      </div>
    </div>
  </div>

  <div class="card-grid">
    <div class="card">
      <div class="card-icon" style="background: #e8f0fe; color: #1a73e8;">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="card-title">Total Pedidos</div>
      <div class="card-value"><?php echo $stats['total_pedidos']; ?></div>
      <div class="card-trend up">
        <i class="fas fa-arrow-up"></i> 12% desde ayer
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #e6f4ea; color: #138a54;">
        <i class="fas fa-dollar-sign"></i>
      </div>
      <div class="card-title">Ingresos Totales</div>
      <div class="card-value">$<?php echo number_format($stats['ingresos_totales'], 0, ',', '.'); ?></div>
      <div class="card-trend up">
        <i class="fas fa-arrow-up"></i> 8% desde ayer
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #fef7e0; color: #b3871f;">
        <i class="fas fa-calendar-day"></i>
      </div>
      <div class="card-title">Pedidos Hoy</div>
      <div class="card-value"><?php echo $stats['pedidos_hoy']; ?></div>
      <div class="card-trend down">
        <i class="fas fa-arrow-down"></i> 3% desde ayer
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #fce8e6; color: #d93025;">
        <i class="fas fa-times-circle"></i>
      </div>
      <div class="card-title">Pedidos Cancelados</div>
      <div class="card-value">2</div>
      <div class="card-trend down">
        <i class="fas fa-arrow-down"></i> 5% desde ayer
      </div>
    </div>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h2>Lista de Pedidos</h2>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="buscador" placeholder="Buscar pedidos...">
      </div>
    </div>
    
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Fecha</th>
          <th>Productos</th>
          <th>Total</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php
        while ($fila = $resultado->fetch_assoc()) {
          // Determinar estado aleatorio para el ejemplo
          $estados = ['completed', 'pending', 'cancelled'];
          $estado = $estados[array_rand($estados)];
          
          echo "<tr>";
          echo "<td>#{$fila["id"]}</td>";
          echo "<td>" . date('d/m/Y H:i', strtotime($fila["fecha"])) . "</td>";
          echo "<td><ul style='padding-left: 15px; margin: 0;'>";
          foreach (explode(",", $fila["productos"]) as $item) {
            echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
          }
          echo "</ul></td>";
          echo "<td style='font-weight: bold; color: #e85c00;'>$" . number_format($fila["total"], 0, ',', '.') . "</td>";
          echo "<td><span class='status $estado'>" . 
                ($estado == 'completed' ? 'Completado' : ($estado == 'pending' ? 'Pendiente' : 'Cancelado')) . 
                "</span></td>";
          echo "<td>
                  <button class='action-btn btn-view'><i class='fas fa-eye'></i></button>
                  <button class='action-btn btn-edit'><i class='fas fa-edit'></i></button>
                  <button class='action-btn btn-delete'><i class='fas fa-trash'></i></button>
                </td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <p>Sistema de Administración de Pedidos &copy; <?php echo date('Y'); ?> - Todos los derechos reservados</p>
  </div>
</div>

<script>
document.getElementById("buscador").addEventListener("input", function() {
  const filtro = this.value.toLowerCase();
  document.querySelectorAll("table tbody tr").forEach((fila) => {
    const texto = fila.innerText.toLowerCase();
    fila.style.display = texto.includes(filtro) ? "" : "none";
  });
});
</script>

</body>
</html>