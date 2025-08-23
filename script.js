// === FILTRO DE CATEGORÍAS ===
function filtrarCategoria(categoria, boton) {
  const productos = document.querySelectorAll('.producto');

  productos.forEach(producto => {
    const categoriaProducto = producto.getAttribute('data-categoria');

    if (categoria === 'todo' || categoriaProducto === categoria) {
      producto.style.display = 'block';  // se muestra
    } else {
      producto.style.display = 'none';   // se esconde
    }
  });

  // Cambiar color del botón activo
  document.querySelectorAll('.filtro').forEach(b => b.classList.remove('activo'));
  if (boton) boton.classList.add('activo');
}

// === BUSCADOR GENERAL (con scroll) ===
document.addEventListener('DOMContentLoaded', function () {
  const buscador = document.getElementById('buscador');
  if (!buscador) return;

  buscador.addEventListener('keyup', function () {
    const texto = this.value.toLowerCase().trim();
    const productos = document.querySelectorAll('.producto');
    let hayCoincidencia = false;

    productos.forEach(function (card) {
      const contenido = card.innerText.toLowerCase();
      if (contenido.includes(texto)) {
        card.style.display = 'block';
        hayCoincidencia = true;
      } else {
        card.style.display = 'none';
      }
    });

    // Si hay texto y al menos una coincidencia → bajar a la sección productos
    if (texto.length > 0 && hayCoincidencia) {
      document.getElementById('productos').scrollIntoView({ behavior: 'smooth' });
    }
  });
});


// === MENSAJE DE CONFIRMACIÓN (Carrito) ===
function mostrarMensajeConfirmacion() {
  const mensaje = document.getElementById('mensaje-confirmacion');
  if (!mensaje) return;

  mensaje.classList.add('mostrar');
  mensaje.style.display = 'block';

  setTimeout(() => {
    mensaje.classList.remove('mostrar');
    mensaje.style.display = 'none';
  }, 2000);
}
// === FILTRO DE CATEGORÍAS ===
function filtrarCategoria(categoria, boton) {
  const productos = document.querySelectorAll('.producto');

  productos.forEach(producto => {
    const categoriaProducto = producto.getAttribute('data-categoria');

    if (categoria === 'todo' || categoriaProducto === categoria) {
      producto.style.display = 'block';  // se muestra
    } else {
      producto.style.display = 'none';   // se esconde
    }
  });

  // Cambiar color del botón activo
  document.querySelectorAll('.filtro').forEach(b => b.classList.remove('activo'));
  if (boton) boton.classList.add('activo');
}

// === BUSCADOR GENERAL (con scroll) ===
document.addEventListener('DOMContentLoaded', function () {
  const buscador = document.getElementById('buscador');
  if (!buscador) return;

  buscador.addEventListener('keyup', function () {
    const texto = this.value.toLowerCase().trim();
    const productos = document.querySelectorAll('.producto');
    let hayCoincidencia = false;

    productos.forEach(function (card) {
      const contenido = card.innerText.toLowerCase();
      if (contenido.includes(texto)) {
        card.style.display = 'block';
        hayCoincidencia = true;
      } else {
        card.style.display = 'none';
      }
    });

    // Si hay texto y al menos una coincidencia → bajar a la sección productos
    if (texto.length > 0 && hayCoincidencia) {
      document.getElementById('productos').scrollIntoView({ behavior: 'smooth' });
    }
  });
});

// === CARRITO ===
let carrito = [];
let total = 0;

// Botones para abrir/cerrar el carrito
document.getElementById("abrirCarrito").addEventListener("click", () => {
  document.getElementById("carrito-panel").classList.add("activo");
});

document.getElementById("cerrarCarrito").addEventListener("click", () => {
  document.getElementById("carrito-panel").classList.remove("activo");
});

// Agregar productos al carrito
function agregarAlCarrito(nombre, precio) {
  carrito.push({ nombre, precio });
  total += precio;

  actualizarCarrito();
}

// Actualizar carrito en pantalla
function actualizarCarrito() {
  // contador
  document.getElementById("carrito-contador").textContent = carrito.length;

  // items
  const items = document.getElementById("carrito-items");
  items.innerHTML = "";

  carrito.forEach((item, i) => {
    const div = document.createElement("div");
    div.classList.add("carrito-item");
    div.innerHTML = `
      <span>${item.nombre} - $${item.precio}</span>
      <button onclick="eliminarDelCarrito(${i})">❌</button>
    `;
    items.appendChild(div);
  });

  if (carrito.length === 0) {
    items.innerHTML = "<p>Tu carrito está vacío</p>";
  }

  // total
  document.getElementById("carrito-total").textContent = total;
}

// Eliminar producto
function eliminarDelCarrito(indice) {
  total -= carrito[indice].precio;
  carrito.splice(indice, 1);
  actualizarCarrito();
}


// === MODALES (Login y Registro) ===
function abrirModal(id) {
  document.getElementById(id).style.display = "block";
}

function cerrarModal(id) {
  document.getElementById(id).style.display = "none";
}

// Cerrar modal al hacer clic afuera
window.onclick = function(event) {
  const modals = document.querySelectorAll(".modal");
  modals.forEach(m => {
    if (event.target === m) {
      m.style.display = "none";
    }
  });
};
// Mostrar / ocultar botón
window.addEventListener("scroll", function () {
  const btnArriba = document.getElementById("btn-arriba");
  if (window.scrollY > 200) {
    btnArriba.classList.add("visible");
  } else {
    btnArriba.classList.remove("visible");
  }
});

// Función para volver arriba con scroll suave
document.getElementById("btn-arriba").addEventListener("click", function () {
  window.scrollTo({ top: 0, behavior: "smooth" });
});






