<?php
include("conexion.php");

$mensaje_exito = "";
$mensaje_error = "";

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $asunto = "Consulta desde contacto"; // Puedes agregar un campo asunto si quieres
    $mensaje = trim($_POST['mensaje']);
    
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        $mensaje_error = "Por favor completa los campos obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje_error = "Por favor ingresa un email válido";
    } else {
        // Insertar en la base de datos
        $stmt = $conn->prepare("INSERT INTO mensajes_contacto (nombre, email, telefono, asunto, mensaje) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $email, $telefono, $asunto, $mensaje);
        
        if ($stmt->execute()) {
            $mensaje_exito = "¡Gracias por tu mensaje! Te contactaremos pronto.";
            // Limpiar el formulario
            $_POST = array();
        } else {
            $mensaje_error = "Error al enviar el mensaje. Por favor intenta nuevamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width,initial-scale=1">
 <meta name="description" content="Huellas de Amor Petshop - Contáctanos para atención al cliente, dudas y asesoría personalizada.">
 <link rel="icon" type="image/png" href="images/favicon.png">

 <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 <link rel="stylesheet" href="style.css">
</head>
<body>

 <!-- Header -->
<header class="topbar">
  <div class="container topbar__inner">
    <a class="brand" href="index.php">
      <img src="images/logo.svg" alt="Huellas de Amor" class="brand__logo" loading="lazy">
      <span class="brand__name">Huellas de Amor</span>
    </a>

    <nav class="nav">
      <a class="nav__link" href="index.php">Inicio</a>
      <a class="nav__link" href="productos.html">Productos</a>
      <a class="nav__link" href="blog.html">Blog</a>
      <a class="nav__link" href="nosotros.html">Nosotros</a>
      <a class="nav__link active" href="contacto.php">Contacto</a>
    </nav>
  </div>
</header>

<main class="contacto">

  <!-- Hero -->
  <section class="contacto-hero">
    <div class="container">
      <h1>Contáctanos</h1>
      <p>Queremos escucharte y ayudarte con lo que tu mascota necesite 🐾</p>
    </div>
  </section>

  <!-- Formulario + Info -->
  <section class="contacto-grid container">
    
    <!-- Formulario -->
    <div class="contacto-form">
      <h2>Envíanos un mensaje</h2>
      
      <?php if ($mensaje_exito): ?>
        <div class="alert-success"><?php echo $mensaje_exito; ?></div>
      <?php endif; ?>
      
      <?php if ($mensaje_error): ?>
        <div class="alert-error"><?php echo $mensaje_error; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_POST['nombre'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label for="email">Correo electrónico</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
          <label for="telefono">Teléfono</label>
          <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($_POST['telefono'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label for="mensaje">Mensaje</label>
          <textarea id="mensaje" name="mensaje" rows="4" required><?php echo htmlspecialchars($_POST['mensaje'] ?? ''); ?></textarea>
        </div>
        <button type="submit" class="btn btn--primary">Enviar</button>
      </form>
    </div>

    <!-- Info contacto -->
    <div class="contacto-info">
      <h2>Información de contacto</h2>
      <p><i class="fa-solid fa-location-dot"></i> CRA. 20 # 45 A 36 SUR - SANTA</p>
      <p><i class="fa-brands fa-whatsapp"></i> WhatsApp: 3160810117</p>
      <p><i class="fa-solid fa-phone"></i> Teléfono: 3128076150</p>
      <p><i class="fa-solid fa-envelope"></i> atencionalcliente@huellasdeamorpets.com</p>
      <p><i class="fa-solid fa-clock"></i> Lun–Sáb: 8am–8pm / Dom–Fest: 9am–6pm</p>
    </div>

  </section>

  <!-- Mapa -->
  <section class="contacto-mapa">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!..." 
      width="100%" height="400" style="border:0;" 
      allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </section>

</main>

<!-- El resto de tu footer se mantiene igual -->