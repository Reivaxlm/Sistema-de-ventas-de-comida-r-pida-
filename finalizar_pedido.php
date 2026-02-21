<?php
include("connection.php");
session_start();

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data) {
    $total = (float)$data['total'];
    $productos = $data['productos'];

    // 1. Guardar el pago
    mysqli_query($conn, "INSERT INTO pago (total_price, reg_date) VALUES ('$total', NOW())");

    foreach ($productos as $item) {
        $nombre_prod = $item['title']; 
        $cantidad_vendida = (int)$item['quantity'];

        // Buscamos el ID (usamos BINARY o exactitud para evitar errores)
        $res_p = mysqli_query($conn, "SELECT id FROM productos_finales WHERE nombre = '$nombre_prod'");
        $p_data = mysqli_fetch_assoc($res_p);
        
        if ($p_data) {
            $id_producto = $p_data['id'];

            // Buscamos receta
            $res_receta = mysqli_query($conn, "SELECT * FROM recetas WHERE id_producto = '$id_producto'");
            
            while ($r = mysqli_fetch_assoc($res_receta)) {
                $id_insumo = $r['id_insumo'];
                $gasto = $r['cantidad_usada'] * $cantidad_vendida;

                // Restar stock
                mysqli_query($conn, "UPDATE inventario SET cantidad = cantidad - $gasto WHERE id = '$id_insumo'");
            }
        }
    }
    echo json_encode(['status' => 'success']);
} else {
    // Si sale este error es porque el JS no está enviando el JSON correctamente
    echo json_encode(['status' => 'error', 'message' => 'No se recibió información del carrito']);
}
?>