const btnCart = document.querySelector('.container-cart-icon');
const containerCartProducts = document.querySelector('.container-cart-products');

btnCart.addEventListener('click', () => {
    containerCartProducts.classList.toggle('hidden-cart');
});

/* ========================= */
const cartInfo = document.querySelector('.cart-product');
const rowProduct = document.querySelector('.row-product');

// Lista de todos los contenedores de productos
const productsList = document.querySelector('.container-items');

// Variable de arreglos de Productos
let allProducts = [];

const valorTotal = document.querySelector('.total-pagar');
const countProducts = document.querySelector('#contador-productos');

const cartEmpty = document.querySelector('.cart-empty');
const cartTotal = document.querySelector('.cart-total');

// Escuchar clics para añadir al carrito
productsList.addEventListener('click', e => {
    if (e.target.classList.contains('btn-add-cart')) {
        const product = e.target.parentElement;

        const infoProduct = {
            quantity: 1,
            title: product.querySelector('h2').textContent,
            price: product.querySelector('p').textContent,
        };

        const exits = allProducts.some(
            product => product.title === infoProduct.title
        );

        if (exits) {
            const products = allProducts.map(product => {
                if (product.title === infoProduct.title) {
                    product.quantity++;
                    return product;
                } else {
                    return product;
                }
            });
            allProducts = [...products];
        } else {
            allProducts = [...allProducts, infoProduct];
        }

        showHTML();
    }
});

// Escuchar clics para eliminar productos
rowProduct.addEventListener('click', e => {
    // Buscamos si se hizo clic en el SVG o en un camino del SVG (path)
    if (e.target.closest('.icon-close')) {
        const productElement = e.target.closest('.cart-product');
        // Importante: El título debe extraerse exactamente de donde se crea en showHTML
        const title = productElement.querySelector('.titulo-producto-carrito').textContent;

        allProducts = allProducts.filter(
            product => product.title !== title
        );

        showHTML();
    }
});
// --- Lógica para el botón "Procesar Compra" ---
document.addEventListener('click', e => {
    if (e.target.classList.contains('boton')) {
        if (allProducts.length === 0) {
            alert("El carrito está vacío");
            e.preventDefault();
            return;
        }

        // GUARDAMOS TODO EL CARRITO (productos y total)
        const totalActual = valorTotal.innerText.replace('$', '');
        localStorage.setItem('carrito_productos', JSON.stringify(allProducts));
        localStorage.setItem('montoFactura', totalActual);

        // AHORA SÍ, vamos a la página del cliente
        window.location.href = 'cliente.html';
    }
});

// Funcion para mostrar HTML
const showHTML = () => {
    // Control de visibilidad del mensaje "Carrito vacío"
    if (!allProducts.length) {
        cartEmpty.classList.remove('hidden');
        rowProduct.classList.add('hidden');
        cartTotal.classList.add('hidden');
    } else {
        cartEmpty.classList.add('hidden');
        rowProduct.classList.remove('hidden');
        cartTotal.classList.remove('hidden');
    }

    // Limpiar HTML previo
    rowProduct.innerHTML = '';

    let total = 0;
    let totalOfProducts = 0;

    allProducts.forEach(product => {
    const containerProduct = document.createElement('div');
    containerProduct.classList.add('cart-product');

    containerProduct.innerHTML = `
        <div class="info-cart-product" style="display: flex; align-items: center; gap: 10px;">
            <span class="cantidad-producto-carrito" style="font-weight:900; background:#fcc404; padding:5px; border:2px solid #000;">
                ${product.quantity}
            </span>
            <div>
                <p class="titulo-producto-carrito" style="margin:0; font-weight:900;">${product.title}</p>
                <span class="precio-producto-carrito" style="color:#dd1919; font-weight:900;">${product.price}</span>
            </div>
        </div>
        <div class="icon-close" style="cursor:pointer; background:#000; color:#fff; padding:5px; display:flex;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
    `;
    rowProduct.append(containerProduct);
    // Cálculo de totales
        total = total + parseInt(product.quantity * product.price.slice(1));
        totalOfProducts = totalOfProducts + product.quantity;
    });

    valorTotal.innerText = `$${total}`;
    countProducts.innerText = totalOfProducts;

    // Guardado en LocalStorage
    localStorage.setItem('montoFactura', total);
};