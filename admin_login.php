<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    if ($usuario === "admin" && $clave === "1234") {
        $_SESSION["admin"] = $usuario;
        header("Location: admin_panel.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<form method="POST">
  <h2>Iniciar sesión como admin</h2>
  <input type="text" name="usuario" placeholder="Usuario">
  <input type="password" name="clave" placeholder="Contraseña">
  <button type="submit">Entrar</button>
  <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</form>
