<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

// Obtener todos los usuarios/clientes
$clientes = $conn->query("SELECT * FROM usuarios ORDER BY creado_en DESC");

// Estadísticas
$total_clientes = $clientes->num_rows;
$clientes_hoy = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE DATE(creado_en) = CURDATE()")->fetch_assoc()['total'];
$con_compras = $conn->query("SELECT COUNT(DISTINCT usuario_id) as total FROM pedidos WHERE usuario_id IS NOT NULL")->fetch_assoc()['total'];
$total_pedidos = $conn->query("SELECT COUNT(*) as total FROM pedidos")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Clientes - Panel Admin</title>
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
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      border-left: 4px solid var(--primary);
    }
    
    .card-icon {
      width: 45px;
      height: 45px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
      font-size: 18px;
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
    
    .card-trend {
      margin-top: 8px;
      font-size: 12px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    
    .card-trend.up { color: var(--success); }
    .card-trend.down { color: var(--danger); }
    
    /* Table */
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
    
    .table-header h2 {
      margin: 0;
      font-size: 18px;
      color: var(--primary);
    }
    
    .search-box {
      position: relative;
    }
    
    .search-box input {
      padding: 8px 15px 8px 35px;
      border: 1px solid #ddd;
      border-radius: 6px;
      width: 250px;
      font-size: 14px;
    }
    
    .search-box i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
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
      font-size: 13px;
    }
    
    tbody tr:hover {
      background-color: #f8f9fa;
    }
    
    .status {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
    }
    
    .status.active {
      background: #e6f4ea;
      color: #138a54;
    }
    
    .status.inactive {
      background: #fce8e6;
      color: #d93025;
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
    
    .btn-view {
      background: #e8f0fe;
      color: #1a73e8;
      border: 1px solid #dadce0;
    }
    
    .btn-view:hover {
      background: #d2e3fc;
    }
    
    .btn-edit {
      background: #e6f4ea;
      color: #138a54;
      border: 1px solid #cce7d7;
    }
    
    .btn-edit:hover {
      background: #d4edda;
    }
    
    .btn-delete {
      background: #fce8e6;
      color: #d93025;
      border: 1px solid #fadbd8;
    }
    
    .btn-delete:hover {
      background: #fadbd8;
    }
    
    .btn-history {
      background: #fff3e0;
      color: #f57c00;
      border: 1px solid #ffe0b2;
    }
    
    .btn-history:hover {
      background: #ffe0b2;
    }
    
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #e9ecef;
    }
    
    .customer-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .customer-details {
      line-height: 1.3;
    }
    
    .customer-name {
      font-weight: 600;
      color: #333;
    }
    
    .customer-email {
      font-size: 12px;
      color: #6c757d;
    }
    
    .pedidos-count {
      background: var(--primary);
      color: white;
      padding: 2px 8px;
      border-radius: 10px;
      font-size: 11px;
      font-weight: 600;
    }
    
    /* Modal */
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    
    .modal-content {
      background: white;
      border-radius: 10px;
      padding: 0;
      max-width: 600px;
      width: 90%;
      max-height: 80vh;
      overflow-y: auto;
    }
    
    .modal-header {
      padding: 20px;
      border-bottom: 1px solid #eee;
      display: flex;
      justify-content: between;
      align-items: center;
    }
    
    .modal-header h3 {
      margin: 0;
      color: var(--primary);
    }
    
    .close-modal {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #6c757d;
    }
    
    .modal-body {
      padding: 20px;
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
        font-size: 18px;
      }
      
      .main-content {
        margin-left: 70px;
      }
    }
    
    @media (max-width: 768px) {
      .card-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      
      .search-box input {
        width: 200px;
      }
      
      .header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
      }
      
      .table-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
      }
    }
    
    @media (max-width: 576px) {
      .card-grid {
        grid-template-columns: 1fr;
      }
      
      .search-box input {
        width: 100%;
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
      <li><a href="admin_panel.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
      <li><a href="pedidos.php"><i class="fas fa-shopping-cart"></i> <span>Pedidos</span></a></li>
      <li><a href="productos_admin.php"><i class="fas fa-box"></i> <span>Productos</span></a></li>
      <li><a href="clientes.php" class="active"><i class="fas fa-users"></i> <span>Clientes</span></a></li>
    </ul>
  </nav>
</div>

<div class="main-content">
  <div class="header">
    <div>
      <h1>Gestión de Clientes</h1>
      <p>Administra los usuarios registrados en tu tienda</p>
    </div>
    <div class="user-info">
      <img src="https://ui-avatars.com/api/?name=Administrador&background=3498db&color=fff" alt="Usuario">
      <div>
        <div>Administrador</div>
        <div style="font-size: 12px; color: #777;">Super Admin</div>
      </div>
    </div>
  </div>

  <!-- Estadísticas -->
  <div class="card-grid">
    <div class="card">
      <div class="card-icon" style="background: #e8f0fe; color: #1a73e8;">
        <i class="fas fa-users"></i>
      </div>
      <div class="card-title">TOTAL CLIENTES</div>
      <div class="card-value"><?= $total_clientes ?></div>
      <div class="card-trend up">
        <i class="fas fa-user-plus"></i> Registrados
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #e6f4ea; color: #138a54;">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="card-title">CON COMPRAS</div>
      <div class="card-value"><?= $con_compras ?></div>
      <div class="card-trend up">
        <i class="fas fa-chart-line"></i> Activos
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #fff3e0; color: #f57c00;">
        <i class="fas fa-calendar-day"></i>
      </div>
      <div class="card-title">NUEVOS HOY</div>
      <div class="card-value"><?= $clientes_hoy ?></div>
      <div class="card-trend up">
        <i class="fas fa-bolt"></i> Recientes
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #f3e5f5; color: #7b1fa2;">
        <i class="fas fa-file-invoice-dollar"></i>
      </div>
      <div class="card-title">TOTAL PEDIDOS</div>
      <div class="card-value"><?= $total_pedidos ?></div>
      <div class="card-trend up">
        <i class="fas fa-receipt"></i> Historial
      </div>
    </div>
  </div>

  <!-- Lista de clientes -->
  <div class="table-container">
    <div class="table-header">
      <h2>Lista de Clientes (<?= $total_clientes ?>)</h2>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="buscador" placeholder="Buscar clientes...">
      </div>
    </div>
    
    <table>
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Información de Contacto</th>
          <th>Fecha Registro</th>
          <th>Pedidos</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($cliente = $clientes->fetch_assoc()): 
          // Obtener pedidos del cliente
          $pedidos_cliente = $conn->query("SELECT COUNT(*) as total, SUM(total) as total_gastado FROM pedidos WHERE usuario_id = " . $cliente['id']);
          $pedidos_data = $pedidos_cliente->fetch_assoc();
          $total_pedidos_cliente = $pedidos_data['total'] ?? 0;
          $total_gastado = $pedidos_data['total_gastado'] ?? 0;
        ?>
        <tr>
          <td>
            <div class="customer-info">
              <img src="https://ui-avatars.com/api/?name=<?= urlencode($cliente['nombre']) ?>&background=3498db&color=fff" 
                   alt="<?= htmlspecialchars($cliente['nombre']) ?>" 
                   class="user-avatar">
              <div class="customer-details">
                <div class="customer-name"><?= htmlspecialchars($cliente['nombre']) ?></div>
                <div class="customer-email">@<?= htmlspecialchars($cliente['usuario'] ?? 'usuario') ?></div>
              </div>
            </div>
          </td>
          <td>
            <div style="font-size: 13px;">
              <div><i class="fas fa-envelope text-muted"></i> <?= htmlspecialchars($cliente['email']) ?></div>
              <?php if (!empty($cliente['telefono'])): ?>
                <div><i class="fas fa-phone text-muted"></i> <?= htmlspecialchars($cliente['telefono']) ?></div>
              <?php endif; ?>
            </div>
          </td>
          <td>
            <div style="font-size: 13px;">
              <div><?= date('d/m/Y', strtotime($cliente['creado_en'])) ?></div>
              <div class="text-muted"><?= date('H:i', strtotime($cliente['creado_en'])) ?></div>
            </div>
          </td>
          <td>
            <?php if ($total_pedidos_cliente > 0): ?>
              <div style="font-size: 13px;">
                <div><span class="pedidos-count"><?= $total_pedidos_cliente ?> pedidos</span></div>
                <div class="text-success">$<?= number_format($total_gastado, 0, ',', '.') ?> gastado</div>
              </div>
            <?php else: ?>
              <span style="color: #6c757d; font-size: 12px;">Sin pedidos</span>
            <?php endif; ?>
          </td>
          <td>
            <span class="status active">
              <i class="fas fa-check-circle"></i> Activo
            </span>
          </td>
          <td>
            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
              <button class="action-btn btn-history" onclick="verHistorial(<?= $cliente['id'] ?>, '<?= htmlspecialchars($cliente['nombre']) ?>')" title="Historial de Compras">
                <i class="fas fa-history"></i>
              </button>
              <button class="action-btn btn-view" onclick="verCliente(<?= $cliente['id'] ?>)" title="Ver Detalles">
                <i class="fas fa-eye"></i>
              </button>
              <button class="action-btn btn-edit" onclick="editarCliente(<?= $cliente['id'] ?>)" title="Editar">
                <i class="fas fa-edit"></i>
              </button>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    
    <?php if ($total_clientes == 0): ?>
      <div class="text-center py-5">
        <i class="fas fa-users fa-3x text-muted mb-3"></i>
        <h5>No hay clientes registrados</h5>
        <p class="text-muted">Los clientes aparecerán aquí cuando se registren en tu tienda.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal para historial -->
<div class="modal-overlay" id="historialModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="modalTitle">Historial de Compras</h3>
      <button class="close-modal" onclick="cerrarModal()">&times;</button>
    </div>
    <div class="modal-body">
      <div id="modalContent">
        <!-- Contenido dinámico -->
      </div>
    </div>
  </div>
</div>

<script>
// Búsqueda en tiempo real
document.getElementById("buscador").addEventListener("input", function() {
  const filtro = this.value.toLowerCase();
  document.querySelectorAll("table tbody tr").forEach((fila) => {
    const texto = fila.innerText.toLowerCase();
    fila.style.display = texto.includes(filtro) ? "" : "none";
  });
});

// Funciones para los botones
function verCliente(id) {
  alert('Ver detalles del cliente #' + id + '\n\nAquí puedes mostrar información detallada del cliente.');
}

function editarCliente(id) {
  alert('Editar cliente #' + id + '\n\nAquí puedes implementar la edición de datos del cliente.');
}

function verHistorial(clienteId, clienteNombre) {
  // Simular carga de historial (en un caso real, harías una petición AJAX)
  const modal = document.getElementById('historialModal');
  const modalTitle = document.getElementById('modalTitle');
  const modalContent = document.getElementById('modalContent');
  
  modalTitle.textContent = `Historial de Compras - ${clienteNombre}`;
  
  // Simular datos de historial
  modalContent.innerHTML = `
    <div style="margin-bottom: 20px;">
      <h4 style="color: #333; margin-bottom: 15px;">Resumen del Cliente</h4>
      <div class="card-grid" style="grid-template-columns: repeat(3, 1fr);">
        <div class="card">
          <div class="card-title">TOTAL PEDIDOS</div>
          <div class="card-value">5</div>
        </div>
        <div class="card">
          <div class="card-title">TOTAL GASTADO</div>
          <div class="card-value">$125.000</div>
        </div>
        <div class="card">
          <div class="card-title">PROMEDIO POR PEDIDO</div>
          <div class="card-value">$25.000</div>
        </div>
      </div>
    </div>
    
    <h4 style="color: #333; margin-bottom: 15px;">Últimos Pedidos</h4>
    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
      <div style="display: grid; gap: 10px;">
        <div style="display: flex; justify-content: space-between; padding: 10px; background: white; border-radius: 6px;">
          <div>
            <strong>Pedido #22</strong><br>
            <small class="text-muted">Snack Natural Gatos, Comida Premium</small>
          </div>
          <div style="text-align: right;">
            <strong class="text-success">$12.500</strong><br>
            <small class="text-muted">31/10/2025</small>
          </div>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 10px; background: white; border-radius: 6px;">
          <div>
            <strong>Pedido #21</strong><br>
            <small class="text-muted">Comida Gato Premium 1kg</small>
          </div>
          <div style="text-align: right;">
            <strong class="text-success">$25.000</strong><br>
            <small class="text-muted">31/10/2025</small>
          </div>
        </div>
      </div>
    </div>
    
    <div style="margin-top: 20px; text-align: center;">
      <button class="action-btn btn-primary" style="background: #007bff; color: white;">
        <i class="fas fa-download"></i> Exportar Historial
      </button>
    </div>
  `;
  
  modal.style.display = 'flex';
}

function cerrarModal() {
  document.getElementById('historialModal').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
document.getElementById('historialModal').addEventListener('click', function(e) {
  if (e.target === this) {
    cerrarModal();
  }
});
</script>

</body>
</html>