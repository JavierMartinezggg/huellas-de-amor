<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin_login.php");
    exit;
}
include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración - Panel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Usa los mismos estilos de tu panel */
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 20px;
        }
        
        .config-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: var(--primary);
            margin-bottom: 30px;
        }
        
        .config-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }
        
        .config-section h3 {
            color: var(--primary);
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="config-container">
    <h1>⚙️ Configuración del Sistema</h1>
    
    <div class="config-section">
        <h3>📊 Configuración General</h3>
        <p>Aquí puedes configurar los ajustes generales de tu tienda.</p>
        <p><strong>Funcionalidad en desarrollo...</strong></p>
    </div>
    
    <div class="config-section">
        <h3>🎨 Apariencia</h3>
        <p>Personaliza los colores y estilo de tu tienda.</p>
    </div>
    
    <div class="config-section">
        <h3>📧 Notificaciones</h3>
        <p>Configura las notificaciones por email.</p>
    </div>
    
    <div class="config-section">
        <h3>💰 Métodos de Pago</h3>
        <p>Gestiona los métodos de pago disponibles.</p>
    </div>
    
    <a href="admin_panel.php" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: var(--primary); color: white; text-decoration: none; border-radius: 5px;">
        ← Volver al Panel
    </a>
</div>

</body>
</html>