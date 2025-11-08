<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}

include("conexion.php");

// OBTENER NOTIFICACIONES
$notificaciones = [];

// 1. Notificación de stock bajo
$stock_bajo = $conn->query("
    SELECT nombre, stock 
    FROM productos 
    WHERE stock < 10 AND stock > 0
    ORDER BY stock ASC
");

while ($producto = $stock_bajo->fetch_assoc()) {
    $notificaciones[] = [
        'tipo' => 'stock',
        'titulo' => 'Stock Bajo',
        'mensaje' => "{$producto['nombre']} tiene solo {$producto['stock']} unidades",
        'fecha' => date('Y-m-d H:i:s'),
        'leida' => false,
        'url' => 'productos_admin.php'
    ];
}

// 2. Notificación de productos sin stock
$sin_stock = $conn->query("
    SELECT nombre 
    FROM productos 
    WHERE stock = 0
");

if ($sin_stock->num_rows > 0) {
    $notificaciones[] = [
        'tipo' => 'urgente',
        'titulo' => 'Productos Agotados',
        'mensaje' => "Hay {$sin_stock->num_rows} productos sin stock",
        'fecha' => date('Y-m-d H:i:s'),
        'leida' => false,
        'url' => 'productos_admin.php'
    ];
}

// 3. Notificación de pedidos pendientes
$pedidos_pendientes = $conn->query("
    SELECT COUNT(*) as total 
    FROM pedidos 
    WHERE estado = 'pendiente'
")->fetch_assoc()['total'];

if ($pedidos_pendientes > 0) {
    $notificaciones[] = [
        'tipo' => 'pedido',
        'titulo' => 'Pedidos Pendientes',
        'mensaje' => "Tienes {$pedidos_pendientes} pedidos pendientes de revisión",
        'fecha' => date('Y-m-d H:i:s'),
        'leida' => false,
        'url' => 'pedidos.php?estado=pendiente'
    ];
}

// 4. Notificación de nuevos pedidos hoy
$nuevos_pedidos = $conn->query("
    SELECT COUNT(*) as total 
    FROM pedidos 
    WHERE DATE(fecha) = CURDATE()
")->fetch_assoc()['total'];

if ($nuevos_pedidos > 0) {
    $notificaciones[] = [
        'tipo' => 'nuevo',
        'titulo' => 'Nuevos Pedidos Hoy',
        'mensaje' => "{$nuevos_pedidos} nuevos pedidos recibidos hoy",
        'fecha' => date('Y-m-d H:i:s'),
        'leida' => false,
        'url' => 'pedidos.php'
    ];
}

// Procesar marcar como leída
if (isset($_GET['marcar_leida'])) {
    // En un sistema real, guardarías en la BD qué notificaciones se leyeron
    // Por ahora solo redirigimos a la URL de la notificación
    header("Location: {$_GET['url']}");
    exit;
}

// Contar notificaciones no leídas
$total_no_leidas = count($notificaciones);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notificaciones - Panel Admin</title>
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
      min-height: 100vh;
      padding: 20px;
    }
    
    .header {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .badge {
      background: #e74c3c;
      color: white;
      border-radius: 50%;
      padding: 5px 10px;
      font-size: 14px;
      font-weight: bold;
      margin-left: 10px;
    }
    
    .notificaciones-container {
      max-width: 800px;
      margin: 0 auto;
    }
    
    .notificacion {
      background: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-left: 4px solid #3498db;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    
    .notificacion:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }
    
    .notificacion.stock {
      border-left-color: #f39c12;
    }
    
    .notificacion.urgente {
      border-left-color: #e74c3c;
      background: #fff5f5;
    }
    
    .notificacion.pedido {
      border-left-color: #3498db;
    }
    
    .notificacion.nuevo {
      border-left-color: #2ecc71;
    }
    
    .notificacion-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }
    
    .notificacion-titulo {
      font-weight: 600;
      font-size: 16px;
      color: #2c3e50;
    }
    
    .notificacion-fecha {
      font-size: 12px;
      color: #7f8c8d;
    }
    
    .notificacion-mensaje {
      color: #34495e;
      line-height: 1.5;
    }
    
    .notificacion-acciones {
      margin-top: 10px;
      display: flex;
      gap: 10px;
    }
    
    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 12px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background: #3498db;
      color: white;
    }
    
    .btn-success {
      background: #2ecc71;
      color: white;
    }
    
    .btn-warning {
      background: #f39c12;
      color: white;
    }
    
    .sin-notificaciones {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .sin-notificaciones i {
      font-size: 48px;
      color: #bdc3c7;
      margin-bottom: 15px;
    }
    
    .filtros {
      background: white;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    
    .filtro-btn {
      padding: 8px 15px;
      border: 1px solid #ddd;
      border-radius: 20px;
      background: white;
      cursor: pointer;
      transition: all 0.3s;
    }
    
    .filtro-btn.active {
      background: #3498db;
      color: white;
      border-color: #3498db;
    }
  </style>
</head>
<body>

<div class="notificaciones-container">
  <div class="header">
    <div>
      <h1>🔔 Notificaciones del Sistema</h1>
      <p>Mantente informado sobre el estado de tu tienda</p>
    </div>
    <div>
      <span>Total: <?= $total_no_leidas ?> notificaciones</span>
      <?php if ($total_no_leidas > 0): ?>
        <span class="badge"><?= $total_no_leidas ?> nuevas</span>
      <?php endif; ?>
    </div>
  </div>

  <!-- Filtros -->
  <div class="filtros">
    <button class="filtro-btn active" onclick="filtrarNotificaciones('todas')">
      <i class="fas fa-bell"></i> Todas (<?= $total_no_leidas ?>)
    </button>
    <button class="filtro-btn" onclick="filtrarNotificaciones('stock')">
      <i class="fas fa-box"></i> Stock
    </button>
    <button class="filtro-btn" onclick="filtrarNotificaciones('pedido')">
      <i class="fas fa-shopping-cart"></i> Pedidos
    </button>
    <button class="filtro-btn" onclick="filtrarNotificaciones('urgente')">
      <i class="fas fa-exclamation-triangle"></i> Urgentes
    </button>
  </div>

  <!-- Lista de Notificaciones -->
  <?php if (empty($notificaciones)): ?>
    <div class="sin-notificaciones">
      <i class="fas fa-bell-slash"></i>
      <h3>No hay notificaciones</h3>
      <p>¡Todo está bajo control! No hay alertas pendientes.</p>
    </div>
  <?php else: ?>
    <?php foreach ($notificaciones as $index => $notif): ?>
      <div class="notificacion <?= $notif['tipo'] ?>" 
           onclick="marcarLeida(<?= $index ?>, '<?= $notif['url'] ?>')"
           data-tipo="<?= $notif['tipo'] ?>">
        <div class="notificacion-header">
          <div class="notificacion-titulo">
            <?php 
              $iconos = [
                'stock' => '📦',
                'urgente' => '🚨', 
                'pedido' => '🛒',
                'nuevo' => '🆕'
              ];
              echo $iconos[$notif['tipo']] . ' ' . $notif['titulo'];
            ?>
          </div>
          <div class="notificacion-fecha">
            <?= date('H:i', strtotime($notif['fecha'])) ?>
          </div>
        </div>
        <div class="notificacion-mensaje">
          <?= $notif['mensaje'] ?>
        </div>
        <div class="notificacion-acciones">
          <a href="<?= $notif['url'] ?>" class="btn btn-primary">
            <i class="fas fa-eye"></i> Ver Detalles
          </a>
          <button class="btn btn-success" onclick="event.stopPropagation(); marcarLeida(<?= $index ?>, '<?= $notif['url'] ?>')">
            <i class="fas fa-check"></i> Marcar como leída
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
// Filtrar notificaciones
function filtrarNotificaciones(tipo) {
  const notificaciones = document.querySelectorAll('.notificacion');
  const botones = document.querySelectorAll('.filtro-btn');
  
  // Actualizar botones activos
  botones.forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
  
  // Filtrar notificaciones
  notificaciones.forEach(notif => {
    if (tipo === 'todas') {
      notif.style.display = 'block';
    } else {
      notif.style.display = notif.dataset.tipo === tipo ? 'block' : 'none';
    }
  });
}

// Marcar como leída
function marcarLeida(index, url) {
  // Aquí en un sistema real harías una petición AJAX para marcar como leída
  // Por ahora solo redirigimos
  window.location.href = `notificaciones.php?marcar_leida=true&url=${encodeURIComponent(url)}`;
}

// Auto-actualizar cada 2 minutos
setTimeout(() => {
  window.location.reload();
}, 120000);

// Notificación del navegador (si está permitido)
function mostrarNotificacionNavegador(titulo, mensaje) {
  if ("Notification" in window && Notification.permission === "granted") {
    new Notification(titulo, { body: mensaje, icon: '/favicon.ico' });
  }
}

// Solicitar permisos para notificaciones
if ("Notification" in window && Notification.permission === "default") {
  Notification.requestPermission();
}

console.log('🔔 Sistema de notificaciones cargado - Notificaciones: <?= $total_no_leidas ?>');
</script>

</body>
</html>