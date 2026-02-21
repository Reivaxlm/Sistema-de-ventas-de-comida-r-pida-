<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibimos los datos del formulario
    $nombre = $_POST['nombre_cliente'];
    $apellido = $_POST['apellido_cliente'];
    $cedula = $_POST['cedula_cliente'];
    $telefono = $_POST['telefono_cliente'];
    $metodo = $_POST['metodo_pago'];
    $referencia = isset($_POST['referencia']) ? $_POST['referencia'] : 'N/A';
    $monto = $_POST['monto_total'];

    // 2. Recibimos los productos del carrito para el inventario
    $productos_json = $_POST['carrito_datos'];
    $productos = json_decode($productos_json, true);

    // Iniciar transacción de base de datos
    mysqli_begin_transaction($conn);

    try {
        // A. Insertamos el registro del cliente (para la factura)
        $sql = "INSERT INTO cliente (nombre_cliente, apellido_cliente, cedula_cliente, telefono_cliente, metodo_pago, referencia, monto) 
                VALUES ('$nombre', '$apellido', '$cedula', '$telefono', '$metodo', '$referencia', '$monto')";
        mysqli_query($conn, $sql);

        // B. Descontamos el stock según las recetas
        if ($productos) {
            foreach ($productos as $item) {
                $nombre_prod = $item['title'];
                $cantidad_vendida = (int)$item['quantity'];

                $res_p = mysqli_query($conn, "SELECT id FROM productos_finales WHERE nombre = '$nombre_prod'");
                $p_data = mysqli_fetch_assoc($res_p);

                if ($p_data) {
                    $id_producto = $p_data['id'];
                    $res_receta = mysqli_query($conn, "SELECT id_insumo, cantidad_usada FROM recetas WHERE id_producto = '$id_producto'");
                    
                    while ($r = mysqli_fetch_assoc($res_receta)) {
                        $id_insumo = $r['id_insumo'];
                        $total_gasto = $r['cantidad_usada'] * $cantidad_vendida;
                        mysqli_query($conn, "UPDATE inventario SET cantidad = cantidad - $total_gasto WHERE id = '$id_insumo'");
                    }
                }
            }
        }

        mysqli_commit($conn); // Confirmamos los cambios en la BD
        
        // C. REDIRECCIÓN Y LIMPIEZA
        // Usamos JavaScript para limpiar el localStorage (vaciar carrito) y redirigir
        echo "<script>
                localStorage.removeItem('carrito_productos');
                localStorage.removeItem('montoFactura');
                alert('¡Venta procesada con éxito!');
                window.location.href = 'Principal.php'; 
              </script>";
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn); // Si algo falla, no se guarda nada
        echo "Error en el proceso: " . $e->getMessage();
    }
}
?>