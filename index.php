<?php
session_start();
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
  
 

  <!-- Header unificado -->
  <header class="topbar">
    <div class="container topbar__inner">
      <a class="brand" href="index.html">
        <img src="images/logo.svg" alt="Huellas de Amor" class="brand__logo" loading="lazy">
        <span class="brand__name">Huellas de Amor</span>
      </a>

      <nav class="nav">
        <button class="nav__link" data-cat="perros"><i class="fa-solid fa-dog"></i> Perros</button>
        <button class="nav__link" data-cat="gatos"><i class="fa-solid fa-cat"></i> Gatos</button>
        <button class="nav__link" data-cat="aves"><i class="fa-solid fa-dove"></i> Aves</button>
        <button class="nav__link" data-cat="peces"><i class="fa-solid fa-fish"></i> Peces</button>
        <button class="nav__link" data-cat="todos"><i class="fa-solid fa-table-cells-large"></i> Todos</button>
      </nav>

      <div class="actions">
        
        <!-- <-- PEGA EL BOTÓN AQUÍ -->
      <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="mainNav" aria-label="Abrir menú">
        <i class="fa-solid fa-bars"></i>
      </button>
      
        <div class="search">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input id="buscador" type="text" placeholder="Buscar productos…" />
        </div>

     <div class="user-menu">
  <button class="user-icon"><i class="fa-solid fa-user"></i></button>
  <div class="user-dropdown">
    <?php if (isset($_SESSION["usuario_nombre"])): ?>
      <p style="margin:0; padding:5px;">👋 Hola, <?php echo $_SESSION["usuario_nombre"]; ?></p>
      <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
    <?php else: ?>
      <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Entrar</a>
      <a href="register.php"><i class="fa-solid fa-user-plus"></i> Registrarse</a>
    <?php endif; ?>
  </div>
</div>


        <button id="btnCarrito" class="cart">
          <i class="fa-solid fa-cart-shopping"></i>
          <span id="carrito-contador">0</span>
        </button>
      </div>
    </div>
  </header>

  <!-- Slider -->
  <section class="hero">
    <div class="hero__slider" id="slider">
      <div class="hero__slide active" style="--bg:url('images/slide1.jpg')">
        <div class="hero__content container">
          <h1>Cuida a tu mejor amigo</h1>
          <p>Nutrición y bienestar al mejor precio.</p>
          <a class="btn btn--primary" data-cat="perros" href="#catalogo">Comprar ahora</a>
        </div>
      </div>
      <div class="hero__slide" style="--bg:url('images/slide2.jpg')">
        <div class="hero__content container">
          <h1>Todo para gatos felices</h1>
          <p>Rascadores, arenas y juguetes irresistibles.</p>
          <a class="btn btn--primary" data-cat="gatos" href="#catalogo">Ver productos</a>
        </div>
      </div>
      <div class="hero__slide" style="--bg:url('images/slide3.jpg')">
        <div class="hero__content container">
          <h1>Acuarios listos para brillar</h1>
          <p>Filtros, iluminación y decoración.</p>
          <a class="btn btn--primary" data-cat="peces" href="#catalogo">Explorar</a>
        </div>
      </div>
    </div>

    <button class="hero__nav prev" id="prev"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="hero__nav next" id="next"><i class="fa-solid fa-chevron-right"></i></button>
  </section>

  <!-- Beneficios -->
  <section class="benefits">
    <div class="container benefits__grid">
      <div class="benefit"><i class="fa-solid fa-truck-fast"></i> Entrega el mismo día</div>
      <div class="benefit"><i class="fa-solid fa-shield"></i> Compra segura</div>
      <div class="benefit"><i class="fa-solid fa-hand-holding-dollar"></i> Pago contra entrega</div>
      <div class="benefit"><i class="fa-solid fa-rotate"></i> Cambios fáciles</div>
    </div>
  </section>

  <!-- Categorías destacadas -->
  <section class="featured-cats container">
    <h2 class="section-title">Busca por tu mascota</h2>
    <div class="featured-cats__grid">
      <button class="cat-card" data-cat="perros">
        <img src="images/cat-perros.png" alt="Perros" loading="lazy"><span>Perros</span>
      </button>
      <button class="cat-card" data-cat="gatos">
        <img src="images/cat-gatos.png" alt="Gatos" loading="lazy"><span>Gatos</span>
      </button>
      <button class="cat-card" data-cat="aves">
        <img src="images/cat-aves.png" alt="Aves" loading="lazy"><span>Aves</span>
      </button>
      <button class="cat-card" data-cat="peces">
        <img src="images/cat-peces.png" alt="Peces" loading="lazy"><span>Peces</span>
      </button>
    </div>
  </section>

  <!-- Subcategorías -->
  <section class="subcats container" id="subcategorias">
    <h2 class="section-title">Categorías Destacadas</h2>
    <div class="subcats__grid">
      <button class="subcat-card" data-cat="alimento-perro">
        <img src="images/cat-alimento-perro.png" alt="Alimento perro" loading="lazy">
        <span>Alimentos perro</span>
      </button>
      <button class="subcat-card" data-cat="alimento-gatos">
        <img src="images/cat-alimento-gato.png" alt="Alimento gatos" loading="lazy">
        <span>Alimentos gatos</span>
      </button>
      <button class="subcat-card" data-cat="snacks">
        <img src="images/cat-snacks.png" alt="Snacks cremosos" loading="lazy">
        <span>Snacks cremosos</span>
      </button>
      <button class="subcat-card" data-cat="congelado">
        <img src="images/cat-congelado.png" alt="Alimento congelado" loading="lazy">
        <span>Alimento congelado</span>
      </button>
      <button class="subcat-card" data-cat="antipulgas">
        <img src="images/cat-antipulgas.png" alt="Antipulgas" loading="lazy">
        <span>Antipulgas</span>
      </button>
      <button class="subcat-card" data-cat="paseo">
        <img src="images/cat-paseo.png" alt="Paseo" loading="lazy">
        <span>Paseo</span>
      </button>
      <button class="subcat-card" data-cat="juguetes">
        <img src="images/cat-juguetes.png" alt="Juguetes" loading="lazy">
        <span>Juguetes</span>
      </button>
      <button class="subcat-card" data-cat="arenas">
        <img src="images/cat-arenas.png" alt="Arenas" loading="lazy">
        <span>Arenas</span>
      </button>
      <button class="subcat-card" data-cat="ofertas">
        <img src="images/cat-ofertas.png" alt="Ofertas del mes" loading="lazy">
        <span>Ofertas del mes</span>
      </button>
      <button class="subcat-card" data-cat="combos">
        <img src="images/cat-combos.png" alt="Combos de locura" loading="lazy">
        <span>Combos de locura</span>
      </button>
    </div>
  </section>

  <!-- Catálogo -->
  <section class="catalogo container" id="catalogo" hidden>
    <div class="catalogo__header">
      <h2 class="section-title" id="tituloCatalogo">Catálogo</h2>
      <small id="contadorResultados"></small>
    </div>
    <div class="grid" id="gridProductos"></div>
  </section>

<!-- Ofertas -->
<section class="ofertas container">
  <h2>Ofertas</h2>
  <div class="ofertas-grid">
    <?php
    include("conexion.php");
    $sql = "SELECT * FROM productos WHERE descuento > 0 LIMIT 6";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
      while ($producto = $resultado->fetch_assoc()) {
        $precio_final = $producto['precio'] - ($producto['precio'] * $producto['descuento'] / 100);
        ?>
        <div class="oferta-card">
          <span class="badge-descuento"><?php echo $producto['descuento']; ?>%</span>
          <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>" loading="lazy">
          <h3><?php echo $producto['nombre']; ?></h3>
          <p class="precio">
            $<?php echo number_format($precio_final, 0, ',', '.'); ?>
            <span class="precio-antiguo">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></span>
          </p>
          <button class="btn-oferta" data-add="<?php echo $producto['id']; ?>">Agregar</button>
        </div>
        <?php
      }
    } else {
      echo "<p>No hay ofertas disponibles.</p>";
    }
    ?>
  </div>
</section>



<!-- Recomendados -->
 
<section class="recomendados container">
  <h2 class="section-title">Productos recomendados</h2>
  <div class="carrusel-wrapper">
    <button class="carrusel-btn prev" id="btnPrev">&#10094;</button>
    <div class="carrusel" id="carruselRecomendados">
      <?php
      include("conexion.php");
      $sql = "SELECT * FROM productos WHERE descuento = 0 ORDER BY creado_en DESC LIMIT 6";
      $resultado = $conn->query($sql);

      if ($resultado->num_rows > 0) {
        while ($producto = $resultado->fetch_assoc()) {
          ?>
          <div class="recomendado-card">
            <img src="<?php echo $producto['imagen']; ?>" 
                 alt="<?php echo $producto['nombre']; ?>" 
                 loading="lazy">
            <h3><?php echo $producto['nombre']; ?></h3>
            <p class="precio">
              $<?php echo number_format($producto['precio'], 0, ',', '.'); ?>
            </p>
            <button class="btn-recomendado" data-add="<?php echo $producto['id']; ?>">Agregar</button>
          </div>
          <?php
        }
      } else {
        echo "<p>No hay productos recomendados por ahora 🐾</p>";
      }
      ?>
    </div>
    <button class="carrusel-btn next" id="btnNext">&#10095;</button>
  </div>
</section>


  <!-- Footer -->
  <footer class="footer">
    <div class="container footer__inner">
      <div class="footer__brand">
        <img src="images/logo.svg" class="brand__logo" alt="Huellas de Amor" loading="lazy">
        <p class="footer__slogan">“Gracias por dejar huellas de amor en la vida de tus mascotas.”</p>
        <p>© 2025 Huellas de Amor. Todos los derechos reservados.</p>
      </div>

      <div class="footer__cols">
        <div>
          <h4>Atención al cliente</h4>
          <ul class="footer__links">
            <li><i class="fa-solid fa-location-dot"></i> CRA. 20 # 45 A 36 SUR - SANTA</li>
            <li><i class="fa-brands fa-whatsapp"></i> WhatsApp: 3160810117</li>
            <li><i class="fa-solid fa-phone"></i> Teléfono: 3128076150</li>
            <li><i class="fa-solid fa-envelope"></i> atencionalcliente@huellasdeamorpets.com</li>
            <li><i class="fa-solid fa-clock"></i> Lun–Sáb: 8am–8pm / Dom–Fest: 9am–6pm</li>
          </ul>
        </div>

        <div>
          <h4>Enlaces rápidos</h4>
          <ul class="footer__links">
            <li><a href="index.html">Inicio</a></li>
            <li><a href="productos.html">Productos</a></li>
            <li><a href="blog.html">Blog</a></li>
            <li><a href="nosotros.html">Nosotros</a></li>
            <li><a href="contacto.html">Contacto</a></li>
          </ul>
        </div>

        <div>
          <h4>Categorías</h4>
          <ul class="footer__links">
            <li><a href="#" class="footer-cat" data-cat="perros">Perros</a></li>
            <li><a href="#" class="footer-cat" data-cat="gatos">Gatos</a></li>
            <li><a href="#" class="footer-cat" data-cat="aves">Aves</a></li>
            <li><a href="#" class="footer-cat" data-cat="peces">Peces</a></li>
          </ul>
        </div>

        <div>
          <h4>Suscríbete</h4>
          <p>Recibe consejos, novedades y amor directo a tu bandeja de entrada 🐾</p>
          <form class="newsletter__form">
            <input type="email" placeholder="Tu correo electrónico" required>
            <button type="submit" class="btn btn--primary">Suscribirme</button>
          </form>
        </div>
      </div>
    </div>

    <div class="footer__bottom">
      <div class="social">
        <a href="#"><i class="fa-brands fa-facebook"></i></a>
        <a href="#"><i class="fa-brands fa-instagram"></i></a>
        <a href="#"><i class="fa-brands fa-tiktok"></i></a>
      </div>

      <div class="footer__policies">
        <a href="terminos.html">Términos y condiciones</a>
        <a href="privacidad.html">Política de privacidad</a>
        <a href="envios.html">Envíos y devoluciones</a>
      </div>

      <div class="payments">
        <img src="images/pay-visa.png" alt="Visa" loading="lazy">
        <img src="images/pay-mastercard.png" alt="MasterCard" loading="lazy">
        <img src="images/pay-pse.png" alt="PSE" loading="lazy">
      </div>
    </div>
  </footer>

 <!-- Carrito lateral -->
<div id="carrito-panel" class="carrito-panel">
  <div class="carrito-header">
    <h3>🛒 Tu Carrito</h3>
    <button id="cerrarCarrito">✖</button>
  </div>

  <div id="carrito-items" class="carrito-items">
    <?php
    session_start();

    if (!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])) {
        echo "<p>Tu carrito está vacío</p>";
    } else {
        foreach ($_SESSION["carrito"] as $index => $producto) {
            echo "<div class='carrito-item'>";
            echo "<img src='{$producto["imagen"]}' alt='{$producto["nombre"]}'>";
            echo "<h4>{$producto["nombre"]}</h4>";
            echo "<span class='precio'>$" . number_format($producto["precio"], 0, ',', '.') . "</span>";
            echo "<button class='btn-eliminar' data-remove='{$index}'>✖ Eliminar</button>";
            echo "</div>";
        }
    }
    ?>
  </div>

  <div class="carrito-footer">
    <p>Total: <span id="carrito-total">$0</span></p>
    <button class="finalizar-compra" id="btnFinalizar">Finalizar compra</button>
  </div>
</div>



  <!-- Scripts -->
  <script src="script.js"></script>

  

</body>
</html>
