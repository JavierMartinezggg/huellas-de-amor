// -------------------- Datos base (demo) --------------------
const DATA = [
  // PERROS
  {id:1, cat:'perros', nombre:'Alimento Seco Adulto 15kg', precio:159000, img:'images/prod/perro-alimento1.png', tags:'alimento perro seco adulto'},
  {id:2, cat:'perros', nombre:'Juguete Pelota Interactiva', precio:35000, img:'images/prod/perro-juguete1.png', tags:'juguete interactivo perro pelota'},
  {id:3, cat:'perros', nombre:'Shampoo Piel Sensible', precio:26000, img:'images/prod/perro-shampoo.png', tags:'higiene shampoo perro piel sensible'},
  {id:4, cat:'perros', nombre:'Collar Ajustable Nylon', precio:18000, img:'images/prod/perro-collar.png', tags:'accesorios collar perro nylon'},

  // GATOS
  {id:11, cat:'gatos', nombre:'Arena Aglomerante 10kg', precio:52000, img:'images/prod/gato-arena.png', tags:'arena gatos higiene'},
  {id:12, cat:'gatos', nombre:'Rascador Deluxe', precio:99000, img:'images/prod/gato-rascador.png', tags:'rascador gatos juego'},
  {id:13, cat:'gatos', nombre:'Alimento Húmedo Pack x12', precio:68000, img:'images/prod/gato-humedo.png', tags:'alimento humedo gatos'},
  {id:14, cat:'gatos', nombre:'Fuente de Agua', precio:115000, img:'images/prod/gato-fuente.png', tags:'fuente agua gatos bowl'},

  // AVES
  {id:21, cat:'aves', nombre:'Mezcla Semillas Canarios', precio:21000, img:'images/prod/ave-semillas.png', tags:'semillas alimento aves'},
  {id:22, cat:'aves', nombre:'Jaula Mediana', precio:149000, img:'images/prod/ave-jaula.png', tags:'jaula aves perchas'},
  {id:23, cat:'aves', nombre:'Bebedero Antiderrame', precio:16000, img:'images/prod/ave-bebedero.png', tags:'bebedero aves comedero'},

  // PECES
  {id:31, cat:'peces', nombre:'Acuario 40L con Luz LED', precio:329000, img:'images/prod/pez-acuario.png', tags:'acuario peces iluminacion'},
  {id:32, cat:'peces', nombre:'Filtro Interno 600L/h', precio:78000, img:'images/prod/pez-filtro.png', tags:'filtro peces bomba'},
  {id:33, cat:'peces', nombre:'Acondicionador Anticloro', precio:19000, img:'images/prod/pez-anticloro.png', tags:'anticloro acondicionador peces'}
];

// Elige 4 destacados
const DESTACADOS = [DATA[0], DATA[11], DATA[22], DATA[31]];

// -------------------- Helpers UI --------------------
const $ = sel => document.querySelector(sel);
const $$ = sel => document.querySelectorAll(sel);

const gridDestacados = $('#gridDestacados');
const gridProductos  = $('#gridProductos');
const catalogo       = $('#catalogo');
const tituloCatalogo = $('#tituloCatalogo');
const contadorRes    = $('#contadorResultados');
const buscador       = $('#buscador');
const contadorCart   = $('#carrito-contador');

// Carrito
let carrito = [];
let categoriaActiva = null;
let productosActuales = [];

// Formato de dinero
const fmt = v => v.toLocaleString('es-CO', {style:'currency', currency:'COP', maximumFractionDigits:0});

// Pintar tarjetas
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
  gridDestacados.innerHTML = DESTACADOS.map(cardHTML).join('');
}

function renderProductos(lista, titulo = 'Catálogo') {
  productosActuales = lista.slice();
  tituloCatalogo.textContent = titulo;
  contadorRes.textContent = `${lista.length} resultados`;
  gridProductos.innerHTML = lista.map(cardHTML).join('');
  catalogo.hidden = lista.length === 0;
}

// -------------------- Categorías --------------------
function activarCategoria(cat) {
  $$('.nav__link').forEach(b => b.classList.toggle('active', b.dataset.cat === cat));
  $$('.cat-card').forEach(b => b.classList.toggle('active', b.dataset.cat === cat));
  categoriaActiva = cat;

  let lista = (cat === 'todos') ? DATA.slice() : DATA.filter(p => p.cat === cat);
  renderProductos(lista, cat === 'todos' ? 'Todos los productos' : `Productos para ${cat}`);
  document.getElementById('catalogo').scrollIntoView({behavior:'smooth'});
}

$$('.nav__link').forEach(b => {
  b.addEventListener('click', () => activarCategoria(b.dataset.cat));
});

$$('.cat-card, .btn--primary[data-cat]').forEach(b => {
  b.addEventListener('click', (e) => {
    e.preventDefault();
    activarCategoria(b.dataset.cat);
  });
});

// -------------------- Buscador --------------------
buscador.addEventListener('input', e => {
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
const panelCarrito = $('#carrito-panel');
const btnCarrito = $('#btnCarrito');
const cerrarCarrito = $('#cerrarCarrito');
const contenedorCarrito = $('#carrito-items');
const totalCarrito = $('#carrito-total');

function renderCarrito(){
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
        <button onclick="eliminarDelCarrito(${i})">✖</button>
      </div>
    `;
  }).join('');
  totalCarrito.textContent = fmt(total);
  contadorCart.textContent = carrito.length;
}

function eliminarDelCarrito(i){
  carrito.splice(i,1);
  renderCarrito();
}

// Abrir y cerrar panel
btnCarrito.addEventListener('click', ()=> {
  renderCarrito();  // refresca siempre
  panelCarrito.classList.add('active');
});
cerrarCarrito.addEventListener('click', ()=> {
  panelCarrito.classList.remove('active');
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') panelCarrito.classList.remove('active');
});

// Agregar producto
document.addEventListener('click', e => {
  const id = e.target.getAttribute('data-add');
  if (!id) return;
  const prod = DATA.find(p => p.id === Number(id));
  if (!prod) return;
  carrito.push(prod);
  renderCarrito();
  e.target.textContent = 'Añadido ✓';
  setTimeout(() => (e.target.textContent = 'Agregar'), 1200);
});

// -------------------- Slider --------------------
const slides = [...document.querySelectorAll('.hero__slide')];
let idx = 0;
function go(n){
  slides[idx].classList.remove('active');
  idx = (n + slides.length) % slides.length;
  slides[idx].classList.add('active');
}
$('#prev').addEventListener('click', ()=>go(idx-1));
$('#next').addEventListener('click', ()=>go(idx+1));
setInterval(()=>go(idx+1), 6000);

// Init
renderDestacados();
// -------------------- Oferta Flash (contador) --------------------
function iniciarContador(duracionSegundos) {
  const contador = document.getElementById('contador');
  let tiempo = duracionSegundos;

  function actualizar() {
    const horas = Math.floor(tiempo / 3600);
    const minutos = Math.floor((tiempo % 3600) / 60);
    const segundos = tiempo % 60;
    contador.textContent = `${horas}h ${minutos}m ${segundos}s`;
    if (tiempo > 0) tiempo--; else clearInterval(timer);
  }
  actualizar();
  const timer = setInterval(actualizar, 1000);
}
iniciarContador(7200); // 2 horas

// -------------------- Productos Recomendados --------------------
const carrusel = document.getElementById('carruselRecomendados');
if (carrusel) {
  const randoms = DATA.sort(() => 0.5 - Math.random()).slice(0,6);
  carrusel.innerHTML = randoms.map(cardHTML).join('');
}

// -------------------- Newsletter --------------------
document.querySelector('.newsletter__form')?.addEventListener('submit', e => {
  e.preventDefault();
  alert('¡Gracias por suscribirte! 🎉');
  e.target.reset();

});
// ---------------- MAPEO SUBCATEGORÍAS A CATEGORÍAS REALES ----------------
const MAP_SUBCAT = {
  "alimento-perro": "perros",
  "alimento-gatos": "gatos",
  "snacks": "perros",     
  "congelado": "perros",  
  "antipulgas": "perros", 
  "paseo": "perros",
  "juguetes": "perros",   
  "arenas": "gatos",
  "ofertas": "aves",      
  "combos": "peces"       
};

// Eventos para las subcategorías destacadas
document.querySelectorAll(".subcat-card").forEach(btn => {
  btn.addEventListener("click", () => {
    const subcat = btn.dataset.cat;
    const catReal = MAP_SUBCAT[subcat];

    if (!catReal) return;

    activarCategoria(catReal);
  });
});
