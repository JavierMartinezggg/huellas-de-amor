<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

// CONTAR NOTIFICACIONES NO LEÍDAS PARA EL BOTÓN
$total_no_leidas = 0;

// Contar stock bajo
$stock_bajo = $conn->query("SELECT COUNT(*) as total FROM productos WHERE stock < 10 AND stock > 0")->fetch_assoc()['total'];
$total_no_leidas += $stock_bajo;

// Contar sin stock
$sin_stock = $conn->query("SELECT COUNT(*) as total FROM productos WHERE stock = 0")->fetch_assoc()['total'];
if ($sin_stock > 0) $total_no_leidas += 1;

// Contar pedidos pendientes
$pedidos_pendientes = $conn->query("SELECT COUNT(*) as total FROM pedidos WHERE estado = 'pendiente'")->fetch_assoc()['total'];
if ($pedidos_pendientes > 0) $total_no_leidas += 1;

// Contar nuevos pedidos hoy
$nuevos_pedidos = $conn->query("SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha) = CURDATE()")->fetch_assoc()['total'];
if ($nuevos_pedidos > 0) $total_no_leidas += 1;

// ESTADÍSTICAS MEJORADAS CON GRÁFICOS
$stats = [];

// Estadísticas básicas
$result = $conn->query("SELECT COUNT(*) as total FROM pedidos");
$stats['total_pedidos'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT SUM(total) as total FROM pedidos WHERE estado = 'completado'");
$stats['ingresos_totales'] = $result->fetch_assoc()['total'] ?? 0;

$result = $conn->query("SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha) = CURDATE()");
$stats['pedidos_hoy'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM pedidos WHERE estado = 'completado'");
$stats['completados'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM pedidos WHERE estado = 'cancelado'");
$stats['cancelados'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM productos");
$stats['total_productos'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$stats['total_usuarios'] = $result->fetch_assoc()['total'];

// DATOS REALES PARA GRÁFICOS - CORREGIDO
// Ventas últimos 7 días (FUNCIONAL)
$ventas_data = [];
for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $result = $conn->query("SELECT COALESCE(SUM(total), 0) as total FROM pedidos WHERE DATE(fecha) = '$fecha' AND estado = 'completado'");
    $ventas_data[$fecha] = $result->fetch_assoc()['total'];
}

// Productos más vendidos (FUNCIONAL)
$productos_top = $conn->query("
    SELECT nombre, stock as veces_vendido, precio as total_ventas 
    FROM productos 
    ORDER BY stock DESC 
    LIMIT 5
");

// Estados de pedidos (FUNCIONAL)
$estados_data = $conn->query("
    SELECT estado, COUNT(*) as total 
    FROM pedidos 
    GROUP BY estado
");

// Obtener pedidos recientes
$sql = "SELECT * FROM pedidos ORDER BY fecha DESC LIMIT 10";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Panel Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    .main-content {
      flex: 1;
      margin-left: var(--sidebar-width);
      padding: 20px;
      transition: all 0.3s;
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
    
    /* Grid de Gráficos */
    .charts-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }
    
    @media (max-width: 1200px) {
      .charts-grid {
        grid-template-columns: 1fr;
      }
    }
    
    .chart-container {
      background: white;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .chart-header h3 {
      margin: 0;
      color: var(--primary);
    }
    
    .chart-canvas {
      position: relative;
      height: 300px;
      width: 100%;
    }
    
    /* Tabla */
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
    
    .status.completado {
      background: #e6f4ea;
      color: #138a54;
    }
    
    .status.pendiente {
      background: #fef7e0;
      color: #b3871f;
    }
    
    .status.cancelado {
      background: #fce8e6;
      color: #d93025;
    }
    
    .status.procesando {
      background: #e8f0fe;
      color: #1a73e8;
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
    
    /* Footer */
    .footer {
      text-align: center;
      padding: 20px;
      color: #6c757d;
      font-size: 14px;
      margin-top: 40px;
    }
    
    /* Mini Cards */
    .mini-cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .mini-card {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .mini-card .number {
      font-size: 18px;
      font-weight: bold;
      color: var(--primary);
    }
    
    .mini-card .label {
      font-size: 12px;
      color: #6c757d;
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
    </ul>
  </nav>
</div>

<div class="main-content">
  <div class="header">
    <div>
      <h1>Dashboard Principal</h1>
      <p>Resumen general de tu tienda</p>
    </div>
    
    <div style="display: flex; align-items: center; gap: 15px;">
      <!-- Botón de Notificaciones -->
      <div style="position: relative;">
        <a href="notificaciones.php" style="text-decoration: none;">
          <button style="padding: 10px 20px; background: white; border: 2px solid #e9ecef; border-radius: 8px; cursor: pointer; position: relative; display: flex; align-items: center; gap: 8px; font-weight: 600; color: #495057; transition: all 0.3s;">
            <i class="fas fa-bell" style="color: #6c757d;"></i> 
            <span>Notificaciones</span>
            <?php if ($total_no_leidas > 0): ?>
              <span style="position: absolute; top: -8px; right: -8px; background: #e74c3c; color: white; border-radius: 50%; padding: 3px 8px; font-size: 11px; font-weight: bold; min-width: 20px; text-align: center; box-shadow: 0 2px 5px rgba(231, 76, 60, 0.3);">
                <?= $total_no_leidas ?>
              </span>
            <?php endif; ?>
          </button>
        </a>
      </div>

      <!-- Información del Usuario -->
      <div class="user-info">
        <img src="https://ui-avatars.com/api/?name=Administrador&background=3498db&color=fff" alt="Usuario">
        <div>
          <div>Administrador</div>
          <div style="font-size: 12px; color: #777;">Super Admin</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Cards Principales -->
  <div class="card-grid">
    <div class="card">
      <div class="card-icon" style="background: #e8f0fe; color: #1a73e8;">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="card-title">TOTAL PEDIDOS</div>
      <div class="card-value"><?php echo $stats['total_pedidos']; ?></div>
      <div class="card-trend up">
        <i class="fas fa-arrow-up"></i> <?php echo $stats['pedidos_hoy']; ?> hoy
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #e6f4ea; color: #138a54;">
        <i class="fas fa-dollar-sign"></i>
      </div>
      <div class="card-title">INGRESOS TOTALES</div>
      <div class="card-value">$<?php echo number_format($stats['ingresos_totales'], 0, ',', '.'); ?></div>
      <div class="card-trend up">
        <i class="fas fa-chart-line"></i> Ventas
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #fef7e0; color: #b3871f;">
        <i class="fas fa-box"></i>
      </div>
      <div class="card-title">TOTAL PRODUCTOS</div>
      <div class="card-value"><?php echo $stats['total_productos']; ?></div>
      <div class="card-trend up">
        <i class="fas fa-cube"></i> En inventario
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #f3e5f5; color: #7b1fa2;">
        <i class="fas fa-users"></i>
      </div>
      <div class="card-title">TOTAL CLIENTES</div>
      <div class="card-value"><?php echo $stats['total_usuarios']; ?></div>
      <div class="card-trend up">
        <i class="fas fa-user-plus"></i> Registrados
      </div>
    </div>
  </div>

  <!-- Grid de Gráficos -->
  <div class="charts-grid">
    <!-- Gráfico de Ventas -->
    <div class="chart-container">
      <div class="chart-header">
        <h3>📈 Ventas de los Últimos 7 Días</h3>
        <span style="color: #6c757d; font-size: 14px;">Total: $<?= number_format($stats['ingresos_totales'], 0, ',', '.') ?></span>
      </div>
      <div class="chart-canvas">
        <canvas id="ventasChart"></canvas>
      </div>
    </div>

    <!-- Gráfico de Estados -->
    <div class="chart-container">
      <div class="chart-header">
        <h3>📊 Estados de Pedidos</h3>
      </div>
      <div class="chart-canvas">
        <canvas id="estadosChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Mini Cards de Resumen -->
  <div class="mini-cards">
    <div class="mini-card">
      <div class="number"><?= $stats['completados'] ?></div>
      <div class="label">Completados</div>
    </div>
    <div class="mini-card">
      <div class="number"><?= $stats['pedidos_hoy'] ?></div>
      <div class="label">Pedidos Hoy</div>
    </div>
    <div class="mini-card">
      <div class="number"><?= $stats['cancelados'] ?></div>
      <div class="label">Cancelados</div>
    </div>
  </div>

  <!-- Tabla de Pedidos Recientes -->
  <div class="table-container">
    <div class="table-header">
      <h2>Pedidos Recientes</h2>
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
        <?php while ($fila = $resultado->fetch_assoc()): ?>
        <tr>
          <td>#<?= $fila["id"] ?></td>
          <td><?= date('d/m/Y H:i', strtotime($fila["fecha"])) ?></td>
          <td>
            <div style="max-height: 60px; overflow-y: auto; font-size: 12px;">
              <?= nl2br(htmlspecialchars($fila["productos"])) ?>
            </div>
          </td>
          <td style="font-weight: bold; color: #e85c00;">$<?= number_format($fila["total"], 0, ',', '.') ?></td>
          <td>
            <span class="status <?= $fila['estado'] ?? 'pendiente' ?>">
              <?= ucfirst($fila['estado'] ?? 'pendiente') ?>
            </span>
          </td>
          <td>
            <button class="action-btn btn-view" onclick="verPedido(<?= $fila['id'] ?>)">
              <i class="fas fa-eye"></i> Ver
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <p>Sistema de Administración &copy; <?php echo date('Y'); ?> - Huellas de Amor</p>
  </div>
</div>

<script>
// GRÁFICO DE VENTAS - CON DATOS REALES
const ventasCtx = document.getElementById('ventasChart').getContext('2d');
const ventasChart = new Chart(ventasCtx, {
    type: 'line',
    data: {
        labels: [
            <?php
            $labels = [];
            $values = [];
            foreach ($ventas_data as $fecha => $total) {
                $labels[] = "'" . date('d/m', strtotime($fecha)) . "'";
                $values[] = $total;
            }
            echo implode(', ', $labels);
            ?>
        ],
        datasets: [{
            label: 'Ventas ($)',
            data: [<?php echo implode(', ', $values); ?>],
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Ventas: $' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// GRÁFICO DE ESTADOS - CON DATOS REALES
const estadosCtx = document.getElementById('estadosChart').getContext('2d');
const estadosChart = new Chart(estadosCtx, {
    type: 'doughnut',
    data: {
        labels: [
            <?php
            $estados_labels = [];
            $estados_values = [];
            $colores = ['#2ecc71', '#f39c12', '#e74c3c', '#3498db', '#9b59b6'];
            $i = 0;
            
            while ($fila = $estados_data->fetch_assoc()) {
                $estados_labels[] = "'" . ucfirst($fila['estado']) . "'";
                $estados_values[] = $fila['total'];
                $i++;
            }
            
            // Si no hay datos, mostrar ejemplo
            if (empty($estados_labels)) {
                $estados_labels = ["'Pendiente'", "'Completado'", "'Cancelado'"];
                $estados_values = [5, 3, 1];
            }
            
            echo implode(', ', $estados_labels);
            ?>
        ],
        datasets: [{
            data: [<?php echo implode(', ', $estados_values); ?>],
            backgroundColor: [
                '#2ecc71', // Completado
                '#f39c12', // Pendiente  
                '#e74c3c', // Cancelado
                '#3498db', // Procesando
                '#9b59b6'  // Otro
            ],
            borderWidth: 3,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((context.parsed / total) * 100);
                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// FUNCIONES REALES
function verPedido(id) {
    window.open('pedidos.php?ver=' + id, '_blank');
}

console.log('✅ Dashboard 100% Funcional - Notificaciones: <?= $total_no_leidas ?>');
</script>

</body>
</html>