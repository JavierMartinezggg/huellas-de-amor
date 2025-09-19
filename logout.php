<?php
session_start();
session_unset(); // limpia las variables
session_destroy(); // destruye la sesión

// Redirige al login
header("Location: login.php");
exit;
?>
