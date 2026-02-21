<?php
include("connection.php");
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda | Burger Designers</title>
    <link rel="stylesheet" href="styles.css"> </head>
<body>
    <header>
        <h1>TIENDA</h1>
        <div class="container-icon">
    <div class="container-cart-icon">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-cart">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
        </svg>
        <div class="count-products">
            <span id="contador-productos">0</span>
        </div>
    </div>

    <div class="container-cart-products hidden-cart">
        <div class="row-product">
            </div>

        <div class="cart-total">
            <h3>Total:</h3>
            <span class="total-pagar">$0</span>
        </div>
        <p class="cart-empty">El carrito está vacío</p>
        
<a class="boton" href="cliente.html">Procesar Compra</a>
    </div>
</div>
        </header>
        

    <div class="container-items">
        <?php
        // CONSULTA A LA TABLA DE PRODUCTOS
        $query = mysqli_query($conn, "SELECT * FROM productos_finales");
        
        if(mysqli_num_rows($query) > 0){
            while($p = mysqli_fetch_assoc($query)){
                ?>
                <div class="item">
                    <figure>
                    <img src="img/<?php echo $p['imagen']; ?>" alt="Hamburguesa">
                    </figure>
                    <div class="info-product">
                        <h2><?php echo $p['nombre']; ?></h2>
                        <p class="price">$<?php echo $p['precio']; ?></p>
                        <button class="btn-add-cart">Añadir al carrito</button>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<h3>No hay productos en la base de datos.</h3>";
        }
        ?>
    </div>

    <script src="index.js"></script>
</body>
</html>