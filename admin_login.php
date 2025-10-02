<?php
session_start();
$usuario = "admin";
$clave = "1234";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST["user"] === $usuario && $_POST["pass"] === $clave) {
        $_SESSION["admin"] = true;
        header("Location: admin_panel.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<form method="POST">
  <h2>🔐 Acceso administrador</h2>
  <input type="text" name="user" placeholder="Usuario"><br>
  <input type="password" name="pass" placeholder="Contraseña"><br>
  <button type="submit">Ingresar</button>
  <?php if (isset($error)) echo "<p>$error</p>"; ?>
</form>
