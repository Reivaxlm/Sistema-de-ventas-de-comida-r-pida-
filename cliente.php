<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recibimos los datos (Asegúrate que coincidan con tu tabla cliente)
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre_cliente']);
    $apellido = mysqli_real_escape_string($conn, $_POST['apellido_cliente']);
    $cedula = mysqli_real_escape_string($conn, $_POST['cedula_cliente']);
    $telefono = mysqli_real_escape_string($conn, $_POST['telefono_cliente']);
    $metodo = $_POST['metodo_pago'];
    $referencia = isset($_POST['referencia']) ? $_POST['referencia'] : 'N/A';
    
    // CAPTURA DEL MONTO: No tocamos los nombres de tus inputs
    $monto_final = isset($_POST['monto_total']) ? $_POST['monto_total'] : (isset($_POST['monto']) ? $_POST['monto'] : '0');
    $monto_limpio = str_replace(',', '.', $monto_final);

    $productos_json = isset($_POST['carrito_datos']) ? $_POST['carrito_datos'] : '[]';
    $productos = json_decode($productos_json, true);

    mysqli_begin_transaction($conn);

    try {
        // A. Insertar Cliente (Usando tus columnas actuales)
        $sql_cliente = "INSERT INTO cliente (nombre_cliente, apellido_cliente, cedula_cliente, telefono_cliente, metodo_pago, referencia, monto) 
                        VALUES ('$nombre', '$apellido', '$cedula', '$telefono', '$metodo', '$referencia', '$monto_limpio')";
        
        if (!mysqli_query($conn, $sql_cliente)) {
            throw new Exception("Error en registro: " . mysqli_error($conn));
        }

        // B. REDUCCIÓN DE INVENTARIO (UNIDADES Y KILOS)
        if (!empty($productos)) {
            foreach ($productos as $item) {
                $nombre_prod = mysqli_real_escape_string($conn, trim($item['title']));
                $cantidad_vendida = (int)$item['quantity'];

                $res_p = mysqli_query($conn, "SELECT id FROM productos_finales WHERE nombre = '$nombre_prod'");
                $p_data = mysqli_fetch_assoc($res_p);

                if ($p_data) {
                    $id_producto = $p_data['id'];
                    
                    // Buscamos la receta del producto
                    $res_receta = mysqli_query($conn, "SELECT id_insumo, cantidad_consumo FROM recetas WHERE id_producto = '$id_producto'");
                    
                    while ($r = mysqli_fetch_assoc($res_receta)) {
                        $id_insumo = $r['id_insumo'];
                        
                        // --- AQUÍ ESTÁ EL CAMBIO PARA LOS KILOS ---
                        // Forzamos que la cantidad de la receta sea decimal (float)
                        $gasto_receta = (float)$r['cantidad_consumo']; 
                        
                        // Calculamos el gasto total (Ej: 0.2 kg * 2 hamburguesas = 0.4 kg)
                        $total_descontar = $gasto_receta * $cantidad_vendida;
                        
                        // Actualizamos el inventario restando el valor decimal exacto
                        $sql_update = "UPDATE inventario SET cantidad = cantidad - $total_descontar WHERE id = '$id_insumo'";
                        mysqli_query($conn, $sql_update);
                    }
                }
            }
        }

        mysqli_commit($conn); 
        echo "<script>
                // 1. Limpiamos el carrito del navegador
                localStorage.removeItem('carrito_productos');
                localStorage.removeItem('montoFactura');
                
                // 2. Abrimos la factura en una PESTAÑA NUEVA
                window.open('factura.php?id=" . $id_factura . "', '_blank');
                
                // 3. Mandamos al vendedor de vuelta a la pantalla de ventas (Principal.php)
                alert('¡Venta procesada con éxito!');
                window.location.href = 'Principal.php'; 
              </script>";

    } catch (Exception $e) {
        mysqli_rollback($conn); 
        echo "Error: " . $e->getMessage();
    }
}
?>