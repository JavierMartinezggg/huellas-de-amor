<?php
include("conexion.php"); // conexión con la base de datos

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    if ($password !== $confirmar) {
        echo "<script>alert('❌ Las contraseñas no coinciden');</script>";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, email, password) 
                VALUES ('$nombre', '$email', '$password_hash')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('✅ Usuario registrado correctamente');</script>";
        } else {
            echo "❌ Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Huellas de Amor Petshop - Todo para tus mascotas: alimentos, accesorios, juguetes y más.">
  <link rel="icon" type="image/png" href="images/favicon.png">

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <main class="form-container">
    <h1>Registrarse</h1>
    <form method="POST" action="">
      <label>Nombre completo</label>
      <input type="text" name="nombre" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Contraseña</label>
      <input type="password" name="password" required>

      <label>Confirmar contraseña</label>
      <input type="password" name="confirmar" required>

      <button type="submit" class="btn btn--primary">Crear cuenta</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="login.php">Entrar</a></p>
  </main>
</body>
</html>
