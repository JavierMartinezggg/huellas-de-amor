<?php
session_start();
include("conexion.php");

// Procesar login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Buscar usuario
    $sql = "SELECT * FROM usuarios WHERE email = '$email' LIMIT 1";
    $res = $conn->query($sql);

    if ($res->num_rows > 0) {
        $usuario = $res->fetch_assoc();

        // Verificar contraseña
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario'] = $usuario['nombre'];
            header("Location: index.html"); // Aquí puedes redirigir a la página principal
            exit;
        } else {
            $error = "❌ Contraseña incorrecta";
        }
    } else {
        $error = "❌ Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Iniciar sesión en Huellas de Amor Petshop">
  <link rel="icon" type="image/png" href="images/favicon.png">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="form-container">
    <h1>Iniciar sesión</h1>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
      <label>Email</label>
      <input type="email" name="email" required>

      <label>Contraseña</label>
      <input type="password" name="password" required>

      <button type="submit" class="btn btn--primary">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <a href="register.php">Registrarse</a></p>
  </main>
</body>
</html>
