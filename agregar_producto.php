<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

$mensaje = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $descuento = floatval($_POST['descuento']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    
    // Procesar imagen
    $imagen_nombre = "default-product.png"; // Imagen por defecto
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagen_nombre = uniqid() . '.' . $extension;
        $ruta_destino = "images/" . $imagen_nombre;
        
        // Mover archivo subido
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            // Éxito al subir
        } else {
            $mensaje = "Error al subir la imagen";
        }
    }
    
    // Insertar en la base de datos
    $sql = "INSERT INTO productos (nombre, precio, descuento, categoria, imagen, descripcion, creado_en) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddsss", $nombre, $precio, $descuento, $categoria, $imagen_nombre, $descripcion);
    
    if ($stmt->execute()) {
        header("Location: productos_admin.php?success=Producto agregado correctamente");
        exit;
    } else {
        $mensaje = "Error al agregar el producto: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Agregar Producto - Panel Admin</title>
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
    
    /* Sidebar (mismo estilo que productos_admin.php) */
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
    
    /* Form Container */
    .form-container {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      padding: 30px;
      max-width: 600px;
      margin: 0 auto;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
      transition: border 0.3s;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--secondary);
      outline: none;
      box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
    }
    
    .form-group textarea {
      height: 100px;
      resize: vertical;
    }
    
    .file-input {
      padding: 8px !important;
    }
    
    .btn {
      padding: 12px 24px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background: var(--success);
      color: white;
    }
    
    .btn-primary:hover {
      background: #27ae60;
      transform: translateY(-2px);
    }
    
    .btn-secondary {
      background: #95a5a6;
      color: white;
    }
    
    .btn-secondary:hover {
      background: #7f8c8d;
    }
    
    .form-actions {
      display: flex;
      gap: 15px;
      margin-top: 30px;
    }
    
    .alert {
      padding: 12px 16px;
      border-radius: 4px;
      margin-bottom: 20px;
    }
    
    .alert-error {
      background: #fce8e6;
      color: #d93025;
      border: 1px solid #fadbd8;
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
      .form-actions {
        flex-direction: column;
      }
      
      .header {
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
      <li><a href="clientes.php"><i class="fas fa-users"></i> <span>Clientes</span></a></li>
    </ul>
  </nav>
</div>

<div class="main-content">
  <div class="header">
    <div>
      <h1>Agregar Nuevo Producto</h1>
      <p>Completa el formulario para agregar un producto a tu tienda</p>
    </div>
    <div class="user-info">
      <img src="https://ui-avatars.com/api/?name=Administrador&background=3498db&color=fff" alt="Usuario">
      <div>
        <div>Administrador</div>
        <div style="font-size: 12px; color: #777;">Super Admin</div>
      </div>
    </div>
  </div>

  <div class="form-container">
    <?php if (!empty($mensaje)): ?>
      <div class="alert alert-error">
        <?php echo htmlspecialchars($mensaje); ?>
      </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="nombre">Nombre del Producto *</label>
        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Alimento Premium para Perros">
      </div>

      <div class="form-group">
        <label for="precio">Precio *</label>
        <input type="number" id="precio" name="precio" step="0.01" min="0" required placeholder="Ej: 159000">
      </div>

      <div class="form-group">
        <label for="descuento">Descuento (%)</label>
        <input type="number" id="descuento" name="descuento" min="0" max="100" value="0" placeholder="Ej: 15">
      </div>

      <div class="form-group">
        <label for="categoria">Categoría *</label>
        <select id="categoria" name="categoria" required>
          <option value="">Selecciona una categoría</option>
          <option value="perros">Perros</option>
          <option value="gatos">Gatos</option>
          <option value="aves">Aves</option>
          <option value="peces">Peces</option>
          <option value="otros">Otros</option>
        </select>
      </div>

      <div class="form-group">
        <label for="imagen">Imagen del Producto</label>
        <input type="file" id="imagen" name="imagen" accept="image/*" class="file-input">
        <small style="color: #666; display: block; margin-top: 5px;">
          Formatos aceptados: JPG, PNG, WEBP. Tamaño máximo: 2MB
        </small>
      </div>

      <div class="form-group">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" placeholder="Describe el producto..."></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Guardar Producto
        </button>
        <a href="productos_admin.php" class="btn btn-secondary">
          <i class="fas fa-arrow-left"></i> Volver a Productos
        </a>
      </div>
    </form>
  </div>
</div>

<script>
// Preview de imagen antes de subir
document.getElementById('imagen').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    // Validar tamaño (2MB máximo)
    if (file.size > 2 * 1024 * 1024) {
      alert('La imagen es demasiado grande. Máximo 2MB permitido.');
      this.value = '';
      return;
    }
    
    // Validar tipo
    const tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];
    if (!tiposPermitidos.includes(file.type)) {
      alert('Solo se permiten imágenes JPG, PNG o WEBP.');
      this.value = '';
      return;
    }
  }
});
</script>

</body>
</html>