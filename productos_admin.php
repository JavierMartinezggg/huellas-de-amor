<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

// Procesar eliminación
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM productos WHERE id = $id");
    header("Location: productos_admin.php?success=Producto eliminado");
    exit;
}

// Actualizar stock
if (isset($_POST['actualizar_stock'])) {
    $producto_id = intval($_POST['producto_id']);
    $nuevo_stock = intval($_POST['nuevo_stock']);
    
    $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
    $stmt->bind_param("ii", $nuevo_stock, $producto_id);
    $stmt->execute();
    
    header("Location: productos_admin.php?success=Stock actualizado");
    exit;
}

// Obtener productos
$filtro_categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$filtro_busqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';

$sql = "SELECT * FROM productos WHERE 1=1";

if ($filtro_categoria) {
    $sql .= " AND categoria = '$filtro_categoria'";
}

if ($filtro_busqueda) {
    $sql .= " AND (nombre LIKE '%$filtro_busqueda%' OR descripcion LIKE '%$filtro_busqueda%')";
}

$sql .= " ORDER BY creado_en DESC";
$productos = $conn->query($sql);

// Obtener categorías únicas para el filtro
$categorias = $conn->query("SELECT DISTINCT categoria FROM productos WHERE categoria IS NOT NULL");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Productos - Panel Admin</title>
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
    
    .btn-add {
      background: var(--success);
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-weight: 600;
    }
    
    .btn-add:hover {
      background: #27ae60;
      transform: translateY(-2px);
    }
    
    .product-image {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #e9ecef;
    }
    
    .stock-badge {
      padding: 4px 8px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: bold;
    }
    
    .stock-alto { background: #d4edda; color: #155724; }
    .stock-medio { background: #fff3cd; color: #856404; }
    .stock-bajo { background: #f8d7da; color: #721c24; }
    .stock-agotado { background: #f8f9fa; color: #6c757d; }
    
    .filtros-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
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
    
    .action-btn {
      padding: 6px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
      margin-right: 5px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }
    
    .btn-edit {
      background: #e8f0fe;
      color: #1a73e8;
      border: 1px solid #dadce0;
    }
    
    .btn-delete {
      background: #fce8e6;
      color: #d93025;
      border: 1px solid #fadbd8;
    }
    
    .btn-stock {
      background: #e6f4ea;
      color: #138a54;
      border: 1px solid #cce7d7;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .stat-card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .stat-number {
      font-size: 24px;
      font-weight: bold;
      color: var(--primary);
    }
    
    .stat-label {
      font-size: 14px;
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
      <li><a href="admin_panel.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
      <li><a href="pedidos.php"><i class="fas fa-shopping-cart"></i> <span>Pedidos</span></a></li>
      <li><a href="productos_admin.php" class="active"><i class="fas fa-box"></i> <span>Productos</span></a></li>
      <li><a href="clientes.php"><i class="fas fa-users"></i> <span>Clientes</span></a></li>
    </ul>
  </nav>
</div>

<div class="main-content">
  <div class="header">
    <div>
      <h1>Gestión de Productos</h1>
      <p>Administra el inventario de tu tienda</p>
    </div>
    <a href="agregar_producto.php" class="btn-add">
      <i class="fas fa-plus"></i> Agregar Producto
    </a>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
      <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
  <?php endif; ?>

  <!-- Estadísticas -->
  <?php
  $total_productos = $productos->num_rows;
  $stock_bajo = $conn->query("SELECT COUNT(*) as total FROM productos WHERE stock < 10 AND stock > 0")->fetch_assoc()['total'];
  $sin_stock = $conn->query("SELECT COUNT(*) as total FROM productos WHERE stock = 0")->fetch_assoc()['total'];
  $categorias_count = $conn->query("SELECT COUNT(DISTINCT categoria) as total FROM productos")->fetch_assoc()['total'];
  ?>
  
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-number"><?= $total_productos ?></div>
      <div class="stat-label">Total Productos</div>
    </div>
    <div class="stat-card">
      <div class="stat-number"><?= $categorias_count ?></div>
      <div class="stat-label">Categorías</div>
    </div>
    <div class="stat-card">
      <div class="stat-number text-warning"><?= $stock_bajo ?></div>
      <div class="stat-label">Stock Bajo</div>
    </div>
    <div class="stat-card">
      <div class="stat-number text-danger"><?= $sin_stock ?></div>
      <div class="stat-label">Sin Stock</div>
    </div>
  </div>

  <!-- Filtros -->
  <div class="filtros-container">
    <form method="GET" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Filtrar por categoría:</label>
        <select name="categoria" class="form-select" onchange="this.form.submit()">
          <option value="">Todas las categorías</option>
          <?php while ($cat = $categorias->fetch_assoc()): ?>
            <option value="<?= $cat['categoria'] ?>" <?= $filtro_categoria == $cat['categoria'] ? 'selected' : '' ?>>
              <?= ucfirst($cat['categoria']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Buscar productos:</label>
        <div class="input-group">
          <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o descripción..." value="<?= htmlspecialchars($filtro_busqueda) ?>">
          <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <a href="productos_admin.php" class="btn btn-secondary w-100">Limpiar</a>
      </div>
    </form>
  </div>

  <div class="table-container">
    <div class="table-header">
      <h2>Lista de Productos (<?php echo $productos->num_rows; ?>)</h2>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="buscador" placeholder="Buscar en la tabla...">
      </div>
    </div>
    
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Precio</th>
          <th>Descuento</th>
          <th>Stock</th>
          <th>Categoría</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($producto = $productos->fetch_assoc()): 
          $stock = $producto['stock'] ?? 0;
          $stock_class = '';
          if ($stock == 0) $stock_class = 'stock-agotado';
          elseif ($stock < 10) $stock_class = 'stock-bajo';
          elseif ($stock < 30) $stock_class = 'stock-medio';
          else $stock_class = 'stock-alto';
        ?>
        <tr>
          <td><strong>#<?php echo $producto['id']; ?></strong></td>
          <td>
 <img 
  src="images/<?php echo $producto['imagen']; ?>" 
  alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
  class="product-image"
  onerror="this.onerror=null; this.src='images/imagen-no-disponible.png';"
/>

          <td>
            <div style="font-weight: 600;"><?php echo htmlspecialchars($producto['nombre']); ?></div>
            <?php if (!empty($producto['descripcion'])): ?>
              <small class="text-muted"><?= substr($producto['descripcion'], 0, 50) ?>...</small>
            <?php endif; ?>
          </td>
          <td style="font-weight: bold; color: #e85c00;">
            $<?php echo number_format($producto['precio'], 0, ',', '.'); ?>
          </td>
          <td>
            <?php if ($producto['descuento'] > 0): ?>
              <span style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                -<?php echo $producto['descuento']; ?>%
              </span>
            <?php else: ?>
              <span style="color: #7f8c8d; font-size: 12px;">Sin desc.</span>
            <?php endif; ?>
          </td>
          <td>
            <span class="stock-badge <?= $stock_class ?>">
              <?= $stock ?> unidades
            </span>
          </td>
          <td>
            <span style="background: #e8f0fe; color: #1a73e8; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
              <?php echo htmlspecialchars($producto['categoria'] ?? 'General'); ?>
            </span>
          </td>
          <td>
            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
              <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" 
                 class="action-btn btn-edit" title="Editar">
                <i class="fas fa-edit"></i>
              </a>
              <button onclick="actualizarStock(<?= $producto['id'] ?>, <?= $stock ?>)" 
                      class="action-btn btn-stock" title="Actualizar Stock">
                <i class="fas fa-boxes"></i>
              </button>
              <a href="productos_admin.php?eliminar=<?php echo $producto['id']; ?>" 
                 class="action-btn btn-delete"
                 onclick="return confirm('¿Estás seguro de eliminar este producto?')"
                 title="Eliminar">
                <i class="fas fa-trash"></i>
              </a>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    
    <?php if ($productos->num_rows == 0): ?>
      <div class="text-center py-5">
        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
        <h5>No se encontraron productos</h5>
        <p class="text-muted">No hay productos que coincidan con los filtros aplicados.</p>
        <a href="agregar_producto.php" class="btn btn-primary mt-2">
          <i class="fas fa-plus"></i> Agregar Primer Producto
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Búsqueda en tiempo real en la tabla
document.getElementById("buscador").addEventListener("input", function() {
  const filtro = this.value.toLowerCase();
  document.querySelectorAll("table tbody tr").forEach((fila) => {
    const texto = fila.innerText.toLowerCase();
    fila.style.display = texto.includes(filtro) ? "" : "none";
  });
});

function actualizarStock(productoId, stockActual) {
  const nuevoStock = prompt(`Actualizar stock para producto #${productoId}\n\nStock actual: ${stockActual} unidades\nNuevo stock:`, stockActual);
  
  if (nuevoStock !== null && !isNaN(nuevoStock)) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
      <input type="hidden" name="producto_id" value="${productoId}">
      <input type="hidden" name="nuevo_stock" value="${nuevoStock}">
      <input type="hidden" name="actualizar_stock" value="1">
    `;
    document.body.appendChild(form);
    form.submit();
  }
}
</script>

</body>
</html>