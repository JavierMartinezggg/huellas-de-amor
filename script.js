document.addEventListener('DOMContentLoaded', () => {
  
// -------------------- CATEGORÍAS EN FOOTER --------------------
  document.querySelectorAll('.footer-cat').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const cat = link.dataset.cat;
      activarCategoria(cat);       // activa la categoría
      catalogo.hidden = false;     // muestra el catálogo
      catalogo.scrollIntoView({ behavior: 'smooth' }); // baja hasta catálogo
    });
  });

  // -------------------- Datos base (demo) --------------------
  const DATA = [
    {id:1, cat:'perros', nombre:'Alimento Seco Adulto 15kg', precio:159000, img:'images/prod/perro-alimento1.png', tags:'alimento perro seco adulto'},
    {id:2, cat:'perros', nombre:'Juguete Pelota Interactiva', precio:35000, img:'images/prod/perro-juguete1.png', tags:'juguete interactivo perro pelota'},
    {id:3, cat:'perros', nombre:'Shampoo Piel Sensible', precio:26000, img:'images/prod/perro-shampoo.png', tags:'higiene shampoo perro piel sensible'},
    {id:4, cat:'perros', nombre:'Collar Ajustable Nylon', precio:18000, img:'images/prod/perro-collar.png', tags:'accesorios collar perro nylon'},
    {id:11, cat:'gatos', nombre:'Arena Aglomerante 10kg', precio:52000, img:'images/prod/gato-arena.png', tags:'arena gatos higiene'},
    {id:12, cat:'gatos', nombre:'Rascador Deluxe', precio:99000, img:'images/prod/gato-rascador.png', tags:'rascador gatos juego'},
    {id:13, cat:'gatos', nombre:'Alimento Húmedo Pack x12', precio:68000, img:'images/prod/gato-humedo.png', tags:'alimento humedo gatos'},
    {id:14, cat:'gatos', nombre:'Fuente de Agua', precio:115000, img:'images/prod/gato-fuente.png', tags:'fuente agua gatos bowl'},
    {id:21, cat:'aves', nombre:'Mezcla Semillas Canarios', precio:21000, img:'images/prod/ave-semillas.png', tags:'semillas alimento aves'},
    {id:22, cat:'aves', nombre:'Jaula Mediana', precio:149000, img:'images/prod/ave-jaula.png', tags:'jaula aves perchas'},
    {id:23, cat:'aves', nombre:'Bebedero Antiderrame', precio:16000, img:'images/prod/ave-bebedero.png', tags:'bebedero aves comedero'},
    {id:31, cat:'peces', nombre:'Acuario 40L con Luz LED', precio:329000, img:'images/prod/pez-acuario.png', tags:'acuario peces iluminacion'},
    {id:32, cat:'peces', nombre:'Filtro Interno 600L/h', precio:78000, img:'images/prod/pez-filtro.png', tags:'filtro peces bomba'},
    {id:33, cat:'peces', nombre:'Acondicionador Anticloro', precio:19000, img:'images/prod/pez-anticloro.png', tags:'anticloro acondicionador peces'},
    // 🔹 OFERTAS ESPECIALES
    {id:101, cat:'perros', nombre:'Alimento Premium Perros', precio:42390, img:'images/producto1.png', tags:'alimento perro oferta descuento'},
    {id:102, cat:'gatos', nombre:'Snack Natural Gatos', precio:12500, img:'images/producto2.png', tags:'snack gatos oferta descuento'},
    {id:103, cat:'perros', nombre:'Juguete interactivo', precio:18000, img:'images/producto3.png', tags:'juguete perro oferta descuento'},
    {id:104, cat:'perros', nombre:'Accesorio', precio:8900, img:'images/producto4.png', tags:'accesorio perro oferta descuento'},
    {id:105, cat:'peces', nombre:'Alimento Peces', precio:7200, img:'images/producto5.png', tags:'alimento peces oferta descuento'},
    {id:106, cat:'aves', nombre:'Jaula para aves', precio:59000, img:'images/producto6.png', tags:'jaula aves oferta descuento'}
  ];

  const DESTACADOS = [DATA[0], DATA[11], DATA[22], DATA[31]];

  // -------------------- Helpers UI --------------------
  const $ = s => document.querySelector(s);
  const $$ = s => Array.from(document.querySelectorAll(s));

  const gridDestacados = $('#gridDestacados');
  const gridProductos  = $('#gridProductos');
  const catalogo       = $('#catalogo');
  const tituloCatalogo = $('#tituloCatalogo');
  const contadorRes    = $('#contadorResultados');
  const buscador       = $('#buscador');
  const contadorCart   = $('#carrito-contador');
  const panelCarrito   = $('#carrito-panel');
  const btnCarrito     = $('#btnCarrito');
  const cerrarCarrito  = $('#cerrarCarrito');
  const contenedorCarrito = $('#carrito-items');
  const totalCarrito   = $('#carrito-total');
  const carrusel       = $('#carruselRecomendados');

  let carrito = [];
  let categoriaActiva = null;
  let productosActuales = [];

  const fmt = v => v.toLocaleString('es-CO', {style:'currency', currency:'COP', maximumFractionDigits:0});

  function cardHTML(p) {
    return `
      <article class="card">
        <div class="card__img"><img src="${p.img}" alt="${p.nombre}"></div>
        <h3 class="card__title">${p.nombre}</h3>
        <div class="card__price">${fmt(p.precio)}</div>
        <button class="btn btn--primary" data-add="${p.id}">Agregar</button>
      </article>
    `;
  }

  function renderDestacados() {
    if (gridDestacados) gridDestacados.innerHTML = DESTACADOS.map(cardHTML).join('');
  }

  function renderProductos(lista, titulo = 'Catálogo') {
    productosActuales = lista.slice();
    if (tituloCatalogo) tituloCatalogo.textContent = titulo;
    if (contadorRes) contadorRes.textContent = `${lista.length} resultados`;
    if (gridProductos) gridProductos.innerHTML = lista.map(cardHTML).join('');
    if (catalogo) catalogo.hidden = lista.length === 0;
  }

  // -------------------- Categorías --------------------
  function activarCategoria(cat) {
    $$('.nav__link').forEach(b => b.classList.toggle('active', b.dataset.cat === cat));
    $$('.cat-card').forEach(b => b.classList.toggle('active', b.dataset.cat === cat));
    categoriaActiva = cat;

    const lista = (cat === 'todos') ? DATA.slice() : DATA.filter(p => p.cat === cat);
    renderProductos(lista, cat === 'todos' ? 'Todos los productos' : `Productos para ${cat}`);
    catalogo?.scrollIntoView({behavior:'smooth'});
  }

  $$('.nav__link').forEach(b => b.addEventListener('click', () => activarCategoria(b.dataset.cat)));
  $$('.cat-card').forEach(b => b.addEventListener('click', () => activarCategoria(b.dataset.cat)));

  // -------------------- Subcategorías --------------------
  const SUBCATS = {
    "alimento-perro": [1],
    "alimento-gatos": [13],
    "snacks": [2],
    "congelado": [3],
    "antipulgas": [3],
    "paseo": [4],
    "juguetes": [2, 12],
    "arenas": [11],
    "ofertas": [14, 22, 33],
    "combos": [1, 11, 31]
  };

  const MAP_SUBCAT = {
    "alimento-perro": "perros",
    "alimento-gatos": "gatos",
    "snacks": "perros",
    "congelado": "perros",
    "antipulgas": "perros",
    "paseo": "perros",
    "juguetes": "perros",
    "arenas": "gatos",
    "ofertas": "gatos",
    "combos": "peces"
  };

  $$('.subcat-card').forEach(btn => {
    btn.addEventListener('click', () => {
      const subcat = btn.dataset.cat;
      const ids = SUBCATS[subcat];

      if (ids && ids.length) {
        const productosFiltrados = DATA.filter(p => ids.includes(p.id));
        renderProductos(productosFiltrados, btn.textContent.trim());
      } else {
        const catReal = MAP_SUBCAT[subcat];
        if (catReal) activarCategoria(catReal);
        else {
          gridProductos.innerHTML = `<p class="no-productos">🚫 No hay productos en esta subcategoría</p>`;
          contadorRes.textContent = "0 productos";
          catalogo.hidden = false;
        }
      }
      catalogo.hidden = false;
      tituloCatalogo.textContent = btn.textContent.trim();
      catalogo.scrollIntoView({behavior:'smooth'});
    });
  });

  // -------------------- Buscador --------------------
  buscador?.addEventListener('input', e => {
    const q = e.target.value.trim().toLowerCase();
    if (!q) {
      if (!categoriaActiva) { catalogo.hidden = true; return; }
      activarCategoria(categoriaActiva);
      return;
    }
    const lista = DATA.filter(p => (p.nombre + ' ' + p.tags).toLowerCase().includes(q));
    renderProductos(lista, `Resultados para “${q}”`);
  });

  // -------------------- Carrito --------------------
  function renderCarrito(){
    if (!contenedorCarrito) return;
    if (carrito.length === 0){
      contenedorCarrito.innerHTML = `<p>Tu carrito está vacío</p>`;
      totalCarrito.textContent = "$0";
      contadorCart.textContent = "0";
      return;
    }
    let total = 0;
    contenedorCarrito.innerHTML = carrito.map((p,i)=>{
      total += p.precio;
      return `
        <div class="carrito-item">
          <img src="${p.img}" alt="${p.nombre}">
          <h4>${p.nombre}</h4>
          <span class="precio">${fmt(p.precio)}</span>
          <button data-remove="${i}">✖</button>
        </div>
      `;
    }).join('');
    totalCarrito.textContent = fmt(total);
    contadorCart.textContent = carrito.length;
  }

  document.addEventListener('click', e => {
    const add = e.target.closest('[data-add]');
    if (add) {
      const id = Number(add.getAttribute('data-add'));
      const prod = DATA.find(p => p.id === id);
      if (!prod) return;
      carrito.push(prod);
      renderCarrito();
      add.textContent = 'Añadido ✓';
      setTimeout(()=> add.textContent = 'Agregar', 1200);
      return;
    }

    const rem = e.target.closest('[data-remove]');
    if (rem) {
      const i = Number(rem.getAttribute('data-remove'));
      if (!isNaN(i)) {
        carrito.splice(i,1);
        renderCarrito();
      }
    }
  });

  btnCarrito?.addEventListener('click', ()=> {
    renderCarrito();
    panelCarrito?.classList.add('active');
  });
  cerrarCarrito?.addEventListener('click', ()=> panelCarrito?.classList.remove('active'));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') panelCarrito?.classList.remove('active'); });

  // -------------------- Slider --------------------
  const slides = [...document.querySelectorAll('.hero__slide')];
  let idx = 0;
  function go(n){
    if (!slides.length) return;
    slides[idx].classList.remove('active');
    idx = (n + slides.length) % slides.length;
    slides[idx].classList.add('active');
  }
  $('#prev')?.addEventListener('click', ()=>go(idx-1));
  $('#next')?.addEventListener('click', ()=>go(idx+1));
  setInterval(()=>go(idx+1), 6000);

  // -------------------- Botones del slider --------------------
  $$('.hero__content .btn').forEach(boton => {
    boton.addEventListener('click', e => {
      e.preventDefault();
      const categoria = boton.dataset.cat;
      activarCategoria(categoria);
      catalogo.hidden = false;
      catalogo.scrollIntoView({behavior:'smooth'});
    });
  });

  // -------------------- Recomendados --------------------
  if (carrusel) {
    const randoms = DATA.slice().sort(() => 0.5 - Math.random()).slice(0,6);
    carrusel.innerHTML = randoms.map(cardHTML).join('');
  }

  const btnPrev = $('#btnPrev');
  const btnNext = $('#btnNext');
  if (carrusel && btnPrev && btnNext) {
    btnNext.addEventListener('click', () => {
      carrusel.scrollBy({ left: 220, behavior: "smooth" });
    });
    btnPrev.addEventListener('click', () => {
      carrusel.scrollBy({ left: -220, behavior: "smooth" });
    });
  }

  
  // -------------------- Newsletter --------------------
  document.querySelector('.newsletter__form')?.addEventListener('submit', e => {
    e.preventDefault();
    alert('¡Gracias por suscribirte! 🎉');
    e.target.reset();
  });

  // Init
  renderDestacados();
});
// 🔹 MEJORAS DE INTERACCIÓN - HUELLAS DE AMOR 🔹

// 1. Mejora en la adición al carrito
document.addEventListener('click', (e) => {
  const add = e.target.closest('[data-add]');
  if (add) {
    const id = Number(add.getAttribute('data-add'));
    const prod = DATA.find(p => p.id === id);
    if (!prod) return;
    
    carrito.push(prod);
    renderCarrito();
     

    // Animación de confirmación
    add.classList.add('added');
    add.textContent = '✓ Añadido';
    
    // Animación del contador
    contadorCart.classList.add('added');
    setTimeout(() => {
      contadorCart.classList.remove('added');
    }, 500);
    
    setTimeout(() => {
      add.classList.remove('added');
      add.textContent = 'Agregar';
    }, 1500);
    
    return;
  }
});

// 2. Carga perezosa de imágenes
document.addEventListener('DOMContentLoaded', function() {
  const lazyImages = [].slice.call(document.querySelectorAll('img'));
  
  if ('IntersectionObserver' in window) {
    const lazyImageObserver = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          const lazyImage = entry.target;
          lazyImage.src = lazyImage.dataset.src || lazyImage.src;
          lazyImage.classList.add('lazy-loaded');
          lazyImageObserver.unobserve(lazyImage);
        }
      });
    });
    
    lazyImages.forEach(function(lazyImage) {
      lazyImageObserver.observe(lazyImage);
    });
  }
});

// 3. Mejora en el desplazamiento suave
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// 4. Mejor feedback en formularios
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';
      submitBtn.disabled = true;
    }
  });
});
// === MENU HAMBURGUESA ===
const navToggle = document.getElementById("navToggle");
const nav = document.querySelector(".nav");

navToggle.addEventListener("click", () => {
  nav.classList.toggle("active");

  // accesibilidad
  const expanded = navToggle.getAttribute("aria-expanded") === "true" || false;
  navToggle.setAttribute("aria-expanded", !expanded);
});
// Cerrar menú al hacer clic en un enlace
document.querySelectorAll(".nav__link").forEach(link => {
  link.addEventListener("click", () => {
    nav.classList.remove("active");
    navToggle.setAttribute("aria-expanded", false);
  });
});
document.addEventListener('click', e => {
  const boton = e.target.closest('[data-add]');
  if (boton) {
    const id = boton.getAttribute('data-add');

    fetch('add_to_cart.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `producto_id=${id}`
    })
    .then(res => res.text())
    .then(msg => {
      console.log(msg); // Muestra el mensaje en la consola
      boton.textContent = 'Añadido ✓'; // Cambia el texto del botón
      setTimeout(() => boton.textContent = 'Agregar', 1200); // Lo regresa después de 1.2 segundos
    });
  }
});
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.btn-eliminar').forEach(boton => {
    boton.addEventListener('click', () => {
      const index = boton.getAttribute('data-remove');

      fetch('eliminar_del_carrito.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `producto_id=${index}`
      })
      .then(res => res.text())
      .then(msg => {
        console.log(msg); // Debe decir "Producto eliminado"
        location.reload(); // Recarga el carrito
      });
    });
  });
});
document.getElementById('btnFinalizar').addEventListener('click', () => {
  fetch('finalizar_compra.php')
    .then(res => res.text())
    .then(msg => {
      alert(msg); // Muestra "Pedido registrado con éxito"
      location.reload();
    });
});

// Volver al inicio
function mostrarInicio() {
  const catalogo = document.querySelector("#catalogo");
  catalogo.setAttribute("hidden", true);
  window.scrollTo({ top: 0, behavior: "smooth" });
}
// === Navegación con flechas en Populares ===
document.querySelectorAll(".arrow-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    const target = btn.getAttribute("data-target");
    const container = document.querySelector(`#pop-${target}`);
    const scrollAmount = 300;
    container.scrollBy({
      left: btn.classList.contains("next") ? scrollAmount : -scrollAmount,
      behavior: "smooth"
    });
  });
});
// === Flechas de desplazamiento para "populares" ===
document.querySelectorAll('.pop-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const container = btn.closest('.populares').querySelector('.populares-items');
    const scrollAmount = 300;

    if (btn.dataset.dir === 'left') {
      container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    } else {
      container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }
  });
});
// === Flechas para la sección OFERTAS ===
document.addEventListener('DOMContentLoaded', () => {
  const flechas = document.querySelectorAll('.oferta-arrow');

  flechas.forEach(flecha => {
    flecha.addEventListener('click', () => {
      const targetId = flecha.dataset.target; // ejemplo: "ofertas"
      const contenedor = document.getElementById(targetId);
      if (!contenedor) return;

      const mover = contenedor.clientWidth * 0.8; // mueve el 80% del ancho
      const direccion = flecha.classList.contains('next') ? mover : -mover;

      contenedor.scrollBy({
        left: direccion,
        behavior: 'smooth'
      });
    });
  });
});
// ============================
// FILTRO POR MARCAS - VERSIÓN PROFESIONAL
// ============================

document.addEventListener('DOMContentLoaded', function() {
    // Agregar event listeners a las marcas
    const marcaItems = document.querySelectorAll('.marca-item');
    
    marcaItems.forEach(item => {
        item.addEventListener('click', function() {
            const marca = this.getAttribute('data-marca');
            filtrarPorMarca(marca);
        });
    });
});

async function filtrarPorMarca(marca) {
    try {
        // Mostrar loading
        mostrarLoading(marca);
        
        // Hacer petición a la base de datos
        const response = await fetch(`filtrar_marca.php?marca=${encodeURIComponent(marca)}`);
        const data = await response.json();
        
        if (data.success) {
            mostrarProductosFiltrados(data.productos, marca);
        } else {
            mostrarError('Error al cargar los productos');
        }
        
    } catch (error) {
        console.error('Error:', error);
        mostrarError('Error de conexión');
    }
}

function mostrarLoading(marca) {
    const gridProductos = document.getElementById('gridProductos');
    const catalogo = document.getElementById('catalogo');
    
    // Mostrar catálogo
    catalogo.hidden = false;
    
    // Mostrar loading
    gridProductos.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
            <div class="loading-spinner" style="
                width: 50px; 
                height: 50px; 
                border: 4px solid #f3f3f3; 
                border-top: 4px solid var(--accent); 
                border-radius: 50%; 
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            "></div>
            <h3 style="color: var(--accent); margin-bottom: 10px;">Buscando productos ${marca}</h3>
            <p style="color: var(--muted);">Cargando...</p>
        </div>
    `;
    
    // Hacer scroll al catálogo
    catalogo.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function mostrarProductosFiltrados(productos, marca) {
    const gridProductos = document.getElementById('gridProductos');
    const tituloCatalogo = document.getElementById('tituloCatalogo');
    const contadorRes = document.getElementById('contadorResultados');
    
    // Actualizar título y contador
    tituloCatalogo.textContent = `Marca: ${marca}`;
    contadorRes.textContent = `${productos.length} productos encontrados`;
    
    if (productos.length === 0) {
        gridProductos.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                <div style="font-size: 3rem; margin-bottom: 20px;">🐾</div>
                <h3 style="color: var(--muted); margin-bottom: 10px;">No hay productos de ${marca}</h3>
                <p style="color: var(--muted); margin-bottom: 20px;">Próximamente tendremos stock de esta marca</p>
                <button onclick="mostrarTodosLosProductos()" class="btn btn--primary">
                    Ver todos los productos
                </button>
            </div>
        `;
        return;
    }
    
    // Mostrar productos
    gridProductos.innerHTML = productos.map(producto => {
        const precioFormateado = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        }).format(producto.precio);
        
        const imagenSrc = producto.imagen && producto.imagen !== 'NULL' ? 
                         `images/${producto.imagen}` : 
                         'https://via.placeholder.com/200x200?text=Imagen+No+Disponible';
        
        return `
            <article class="card">
                <div class="card__img">
                    <img src="${imagenSrc}" alt="${producto.nombre}" loading="lazy"
                         onerror="this.src='https://via.placeholder.com/200x200?text=Imagen+No+Disponible'">
                </div>
                <h3 class="card__title">${producto.nombre}</h3>
                <div class="card__price">${precioFormateado}</div>
                <button class="btn btn--primary" data-add="${producto.id}">Agregar al carrito</button>
            </article>
        `;
    }).join('');
    
    // Re-activar los botones de agregar al carrito
    activarBotonesCarrito();
}

function mostrarError(mensaje) {
    const gridProductos = document.getElementById('gridProductos');
    gridProductos.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
            <div style="font-size: 3rem; margin-bottom: 20px;">❌</div>
            <h3 style="color: var(--danger); margin-bottom: 10px;">Error</h3>
            <p style="color: var(--muted); margin-bottom: 20px;">${mensaje}</p>
            <button onclick="mostrarTodosLosProductos()" class="btn btn--primary">
                Volver al catálogo
            </button>
        </div>
    `;
}

function mostrarTodosLosProductos() {
    // Esta función debería recargar todos los productos
    // Por ahora, simulemos recargando la página
    location.reload();
}

function activarBotonesCarrito() {
    // Reactivar la funcionalidad de agregar al carrito
    document.querySelectorAll('[data-add]').forEach(boton => {
        boton.addEventListener('click', function() {
            const id = this.getAttribute('data-add');
            // Tu código existente para agregar al carrito
            console.log('Agregar producto ID:', id);
            
            // Efecto visual
            this.textContent = '✓ Agregado';
            this.style.background = '#4CAF50';
            setTimeout(() => {
                this.textContent = 'Agregar al carrito';
                this.style.background = '';
            }, 2000);
        });
    });
}

// Agregar animación de loading
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
async function buscarYFiltrarCatalogo(termino) {
    try {
        // Mostrar loading
        mostrarLoadingBusqueda(termino);
        
        // ✅ GUARDAR EN EL HISTORIAL (línea clave que falta)
        window.history.pushState({ 
            busqueda: termino 
        }, '', `#buscar-${encodeURIComponent(termino)}`);
        
        // Ocultar todas las secciones excepto header y footer
        ocultarSecciones();
        
        const response = await fetch(`buscar_productos.php?q=${encodeURIComponent(termino)}`);
        const data = await response.json();
        
        if (data.success) {
            mostrarResultadosCompletos(data.productos, termino);
        } else {
            mostrarErrorBusquedaCompleta('Error en la búsqueda');
        }
        
    } catch (error) {
        console.error('Error en búsqueda:', error);
        mostrarErrorBusquedaCompleta('Error de conexión');
    }
}

function mostrarLoadingBusqueda(termino) {
    const gridProductos = document.getElementById('gridProductos');
    const catalogo = document.getElementById('catalogo');
    
    // Mostrar catálogo y ocultar otras secciones
    catalogo.hidden = false;
    
    gridProductos.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px;">
            <div class="loading-spinner" style="
                width: 60px; 
                height: 60px; 
                border: 5px solid #f3f3f3; 
                border-top: 5px solid var(--accent); 
                border-radius: 50%; 
                animation: spin 1s linear infinite;
                margin: 0 auto 30px;
            "></div>
            <h2 style="color: var(--accent); margin-bottom: 15px;">Buscando "${termino}"</h2>
            <p style="color: var(--muted);">Revisando nuestro inventario...</p>
        </div>
    `;
    
    // Hacer scroll al catálogo
    catalogo.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function ocultarSecciones() {
    // Ocultar todas las secciones principales excepto header, catálogo y footer
    const seccionesAOcultar = [
        '.hero',
        '.benefits',
        '.buscador-mascotas',
        '.featured-cats',
        '.subcats',
        '.ofertas',
        '.populares',
        '.recomendados',
        '.marcas-destacadas'
    ];
    
    seccionesAOcultar.forEach(selector => {
        const elementos = document.querySelectorAll(selector);
        elementos.forEach(el => {
            el.style.display = 'none';
        });
    });
    
    // Asegurar que el catálogo esté visible
    const catalogo = document.getElementById('catalogo');
    if (catalogo) {
        catalogo.style.display = 'block';
        catalogo.hidden = false;
    }
}

function mostrarResultadosCompletos(productos, termino) {
    const gridProductos = document.getElementById('gridProductos');
    const tituloCatalogo = document.getElementById('tituloCatalogo');
    const contadorRes = document.getElementById('contadorResultados');
    
    // Actualizar títulos
    tituloCatalogo.textContent = `Resultados para "${termino}"`;
    contadorRes.textContent = `${productos.length} producto${productos.length !== 1 ? 's' : ''} encontrado${productos.length !== 1 ? 's' : ''}`;
    
    if (productos.length === 0) {
        gridProductos.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
                <h2 style="color: var(--muted); margin-bottom: 15px;">No encontramos resultados</h2>
                <p style="color: var(--muted); margin-bottom: 10px;">No hay productos que coincidan con "<strong>${termino}</strong>"</p>
                <p style="color: var(--muted); margin-bottom: 30px; font-size: 0.9rem;">
                    Sugerencias: 
                    <br>• Revisa la ortografía
                    <br>• Usa términos más generales
                    <br>• Explora nuestras categorías
                </p>
                <button onclick="mostrarPaginaCompleta()" class="btn btn--primary" style="padding: 12px 25px;">
                    ← Volver al Catálogo Completo
                </button>
            </div>
        `;
        return;
    }
    
    // Mostrar productos en grid
    gridProductos.innerHTML = productos.map(producto => {
        const precioFormateado = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        }).format(producto.precio);
        
        const imagenSrc = producto.imagen && producto.imagen !== 'NULL' ? 
                         `images/${producto.imagen}` : 
                         'https://via.placeholder.com/200x200?text=Imagen+No+Disponible';
        
        const descripcionCorta = producto.descripcion ? 
                                producto.descripcion.substring(0, 80) + '...' : 
                                'Descripción no disponible';
        
        return `
            <article class="card" style="transition: all 0.3s ease;">
                <div class="card__img">
                    <img src="${imagenSrc}" alt="${producto.nombre}" 
                         onerror="this.src='https://via.placeholder.com/200x200?text=Imagen+No+Disponible'">
                </div>
                <h3 class="card__title">${producto.nombre}</h3>
                <p style="color: var(--muted); font-size: 0.9rem; margin: 5px 0; line-height: 1.4;">
                    ${descripcionCorta}
                </p>
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; margin: 10px 0;">
                    <div class="card__price">${precioFormateado}</div>
                    ${producto.marca ? `
                    <span style="background: #e3f2fd; color: #1976d2; padding: 3px 8px; border-radius: 10px; font-size: 0.8rem; font-weight: 600;">
                        ${producto.marca}
                    </span>
                    ` : ''}
                </div>
                <button class="btn btn--primary" data-add="${producto.id}" 
                        style="width: 100%; margin-top: 10px;">
                    🛒 Agregar
                </button>
            </article>
        `;
    }).join('');
    
    // Reactivar botones de carrito
    activarBotonesCarritoBusqueda(productos);
}

function activarBotonesCarritoBusqueda(productos) {
    document.querySelectorAll('[data-add]').forEach((boton, index) => {
        boton.addEventListener('click', function() {
            const productoId = this.getAttribute('data-add');
            const producto = productos.find(p => p.id == productoId);
            
            if (producto) {
                agregarProductoAlCarrito(producto);
                
                // Efecto visual
                this.innerHTML = '✓ Agregado';
                this.style.background = '#4CAF50';
                
                setTimeout(() => {
                    this.innerHTML = '🛒 Agregar';
                    this.style.background = '';
                }, 2000);
            }
        });
    });
}

function mostrarErrorBusquedaCompleta(mensaje) {
    const gridProductos = document.getElementById('gridProductos');
    gridProductos.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; padding: 80px 20px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">❌</div>
            <h2 style="color: var(--danger); margin-bottom: 15px;">Error en la búsqueda</h2>
            <p style="color: var(--muted); margin-bottom: 30px;">${mensaje}</p>
            <button onclick="mostrarPaginaCompleta()" class="btn btn--primary">
                Volver al Catálogo
            </button>
        </div>
    `;
}

function mostrarPaginaCompleta() {
    // Mostrar nuevamente todas las secciones principales
    const secciones = [
        '.hero',
        '.benefits',
        '.buscador-mascotas',
        '.featured-cats',
        '.subcats',
        '.ofertas',
        '.populares',
        '.recomendados',
        '.marcas-destacadas'
    ];

    secciones.forEach(selector => {
        document.querySelectorAll(selector).forEach(el => {
            el.style.display = ''; // vuelve a mostrar cada sección
        });
    });

    // Ocultar el catálogo
    const catalogo = document.getElementById('catalogo');
    if (catalogo) {
        catalogo.hidden = true;
        catalogo.style.display = 'none';
    }

    // Limpiar el campo de búsqueda
    const buscador = document.getElementById('buscador');
    if (buscador) buscador.value = '';

    // ✅ Quita el #buscar de la URL sin recargar la página
    history.replaceState(null, '', window.location.pathname);

    // Volver suavemente al inicio
    window.scrollTo({ top: 0, behavior: 'smooth' });
}


// También modificar el evento del buscador para que funcione con Enter
document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscador');
    
    if (buscador) {
        // Buscar al presionar Enter
        buscador.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const termino = this.value.trim();
                if (termino.length >= 2) {
                    buscarYFiltrarCatalogo(termino);
                }
            }
        });
        
        // También buscar mientras escribe (opcional)
        let timeoutId;
        buscador.addEventListener('input', function(e) {
            clearTimeout(timeoutId);
            const termino = e.target.value.trim();
            
            if (termino.length >= 3) {
                timeoutId = setTimeout(() => {
                    buscarYFiltrarCatalogo(termino);
                }, 500);
            }
        });
    }
});
// ============================
// Controlar el botón "Atrás" del navegador
// ============================
window.addEventListener('popstate', function(event) {
    if (event.state && event.state.busqueda) {
        // Si el usuario vuelve atrás a una búsqueda anterior
        buscarYFiltrarCatalogo(event.state.busqueda);
    } else {
        // Si no hay búsqueda guardada, mostramos la página completa
        mostrarPaginaCompleta();
    }
});
// ============================================
// 🐾 CONTROL DE NAVEGACIÓN ENTRE CATEGORÍAS Y MARCAS
// ============================================

// Detectar clics en categorías (menú, tarjetas, subcats, footer)
document.querySelectorAll(
  ".nav__link, .cat-card, .subcat-card, .footer-cat"
).forEach(btn => {
  btn.addEventListener("click", e => {
    e.preventDefault();

    const categoria = e.currentTarget.dataset.cat;
    if (!categoria) return;

    // Buscar productos por categoría
    buscarYFiltrarCatalogo(categoria);

    // Guardar en el historial del navegador
    history.pushState({ cat: categoria }, "", `?cat=${encodeURIComponent(categoria)}`);
  });
});

// Detectar clics en marcas destacadas
document.querySelectorAll(".marca-item").forEach(btn => {
  btn.addEventListener("click", e => {
    e.preventDefault();

    const marca = e.currentTarget.dataset.marca;
    if (!marca) return;

    buscarYFiltrarCatalogo(marca);
    history.pushState({ marca: marca }, "", `?marca=${encodeURIComponent(marca)}`);
  });
});

// --- Control del botón "Atrás" del navegador ---
window.addEventListener("popstate", e => {
  if (e.state && e.state.cat) {
    buscarYFiltrarCatalogo(e.state.cat);
  } else if (e.state && e.state.marca) {
    buscarYFiltrarCatalogo(e.state.marca);
  } else {
    mostrarPaginaCompleta();
  }
});

// --- Si el usuario entra con ?cat= o ?marca= ---
document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const cat = params.get("cat");
  const marca = params.get("marca");

  if (cat) {
    buscarYFiltrarCatalogo(cat);
  } else if (marca) {
    buscarYFiltrarCatalogo(marca);
  }
});




