<?php
session_start();
include("conexion.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $clave = $_POST["clave"];

    if ($usuario === "admin" && $clave === "admin123") {
        $_SESSION["admin"] = "admin";
        header("Location: admin_panel.php");
        exit;
    } else {
        $error = "❌ Usuario: admin / Contraseña: admin123";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        input { width: 100%; padding: 12px 15px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 16px; }
        .btn-login { width: 100%; background: #ff6a00; color: white; border: none; padding: 14px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .error { background: #fee; color: #c33; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Panel Administrativo</h2>
        <?php if (!empty($error)): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="post" action="">
            <div class="form-group"><label>Usuario:</label><input type="text" name="usuario" required value="admin"></div>
            <div class="form-group"><label>Contraseña:</label><input type="password" name="clave" required></div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
        <p style="text-align:center; margin-top:15px; color:#666; font-size:14px;">Usuario: <strong>admin</strong><br>Contraseña: <strong>admin123</strong></p>
    </div>
</body>
</html>