<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

// Obtener todos los usuarios/clientes
$clientes = $conn->query("SELECT * FROM usuarios ORDER BY creado_en DESC");
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
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
      margin-right: 5px;
      text-decoration: none;
      display: inline-block;
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
    
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
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

  <div class="card-grid">
    <div class="card">
      <div class="card-icon" style="background: #e8f0fe; color: #1a73e8;">
        <i class="fas fa-users"></i>
      </div>
      <div class="card-title">Total Clientes</div>
      <div class="card-value"><?php echo $clientes->num_rows; ?></div>
      <div class="card-trend up">
        <i class="fas fa-arrow-up"></i> Registrados
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #e6f4ea; color: #138a54;">
        <i class="fas fa-user-check"></i>
      </div>
      <div class="card-title">Clientes Activos</div>
      <div class="card-value"><?php echo $clientes->num_rows; ?></div>
      <div class="card-trend up">
        <i class="fas fa-check-circle"></i> Todos activos
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #fef7e0; color: #b3871f;">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="card-title">Con Compras</div>
      <div class="card-value">
        <?php 
        $result = $conn->query("SELECT COUNT(DISTINCT usuario_id) as total FROM pedidos WHERE usuario_id IS NOT NULL");
        echo $result->fetch_assoc()['total'] ?? 0;
        ?>
      </div>
      <div class="card-trend">
        <i class="fas fa-chart-line"></i> Con historial
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon" style="background: #fce8e6; color: #d93025;">
        <i class="fas fa-user-clock"></i>
      </div>
      <div class="card-title">Nuevos Hoy</div>
      <div class="card-value">
        <?php 
        $result = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE DATE(creado_en) = CURDATE()");
        echo $result->fetch_assoc()['total'] ?? 0;
        ?>
      </div>
      <div class="card-trend up">
        <i class="fas fa-bolt"></i> Recientes
      </div>
    </div>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h2>Lista de Clientes (<?php echo $clientes->num_rows; ?>)</h2>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="buscador" placeholder="Buscar clientes...">
      </div>
    </div>
    
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Fecha Registro</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($cliente = $clientes->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo $cliente['id']; ?></td>
          <td>
            <div style="display: flex; align-items: center; gap: 10px;">
              <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($cliente['nombre']); ?>&background=3498db&color=fff" 
                   alt="<?php echo htmlspecialchars($cliente['nombre']); ?>" 
                   class="user-avatar">
              <span><?php echo htmlspecialchars($cliente['usuario'] ?? $cliente['nombre']); ?></span>
            </div>
          </td>
          <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
          <td><?php echo htmlspecialchars($cliente['email']); ?></td>
          <td><?php echo date('d/m/Y H:i', strtotime($cliente['creado_en'])); ?></td>
          <td>
            <span class="status active">Activo</span>
          </td>
          <td>
            <button class="action-btn btn-view" onclick="verCliente(<?php echo $cliente['id']; ?>)">
              <i class="fas fa-eye"></i>
            </button>
            <button class="action-btn btn-edit" onclick="editarCliente(<?php echo $cliente['id']; ?>)">
              <i class="fas fa-edit"></i>
            </button>
            <button class="action-btn btn-delete" onclick="eliminarCliente(<?php echo $cliente['id']; ?>)">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
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

// Funciones para los botones (placeholder)
function verCliente(id) {
  alert('Ver detalles del cliente #' + id);
  // Aquí puedes implementar un modal o redirección
}

function editarCliente(id) {
  alert('Editar cliente #' + id);
  // Aquí puedes implementar edición de cliente
}

function eliminarCliente(id) {
  if (confirm('¿Estás seguro de eliminar este cliente? Esta acción no se puede deshacer.')) {
    alert('Eliminar cliente #' + id);
    // Aquí puedes implementar eliminación con AJAX
  }
}
</script>

</body>
</html>