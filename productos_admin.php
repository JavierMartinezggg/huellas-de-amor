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

// Obtener productos de la BASE DE DATOS (no del array DATA)
$productos = $conn->query("SELECT * FROM productos ORDER BY creado_en DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Productos - Panel Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* COPIA AQUÍ LOS MISMOS ESTILOS DE TU admin_panel.php */
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
    
    /* COPIA TODOS TUS ESTILOS DEL admin_panel.php aquí */
    /* ... [todo el CSS de tu admin_panel.php] ... */
    
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
    }
    
    .product-image {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 4px;
    }
  </style>
</head>
<body>

<!-- COPIA EL SIDEBAR DE TU admin_panel.php -->
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
      <p>Administra los productos de tu tienda</p>
    </div>
    <a href="agregar_producto.php" class="btn-add">
      <i class="fas fa-plus"></i> Agregar Producto
    </a>
  </div>

  <?php if (isset($_GET['success'])): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px;">
      <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
  <?php endif; ?>

  <div class="table-container">
    <div class="table-header">
      <h2>Lista de Productos (<?php echo $productos->num_rows; ?>)</h2>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="buscador" placeholder="Buscar productos...">
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
          <th>Categoría</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($producto = $productos->fetch_assoc()): ?>
        <tr>
          <td>#<?php echo $producto['id']; ?></td>
          <td>
            <img src="images/<?php echo $producto['imagen']; ?>" 
                 alt="<?php echo $producto['nombre']; ?>" 
                 class="product-image"
                 onerror="this.src='https://via.placeholder.com/50?text=Imagen'">
          </td>
          <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
          <td style="font-weight: bold; color: #e85c00;">
            $<?php echo number_format($producto['precio'], 0, ',', '.'); ?>
          </td>
          <td>
            <?php if ($producto['descuento'] > 0): ?>
              <span style="background: #e74c3c; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px;">
                -<?php echo $producto['descuento']; ?>%
              </span>
            <?php else: ?>
              <span style="color: #7f8c8d;">Sin descuento</span>
            <?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($producto['categoria'] ?? 'General'); ?></td>
          <td>
            <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" 
               class="action-btn btn-edit">
              <i class="fas fa-edit"></i>
            </a>
            <a href="productos_admin.php?eliminar=<?php echo $producto['id']; ?>" 
               class="action-btn btn-delete"
               onclick="return confirm('¿Estás seguro de eliminar este producto?')">
              <i class="fas fa-trash"></i>
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
// Búsqueda en tiempo real (SOLO para el admin, no afecta tu frontend)
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