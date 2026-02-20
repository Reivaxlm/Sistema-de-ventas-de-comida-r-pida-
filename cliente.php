<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibimos los datos con seguridad
    $nombre = $_POST['nombre_cliente'];
    $apellido = $_POST['apellido_cliente'];
    $cedula = $_POST['cedula_cliente'];
    $telefono = $_POST['telefono_cliente'];
    $metodo = $_POST['metodo_pago'];
    $referencia = isset($_POST['referencia']) ? $_POST['referencia'] : 'N/A';
    $monto = $_POST['monto_total'];

    // Insertamos en la tabla 'cliente'
    // IMPORTANTE: Asegúrate de que tu tabla tenga las columnas: referencia y monto
    $sql = "INSERT INTO cliente (nombre_cliente, apellido_cliente, cedula_cliente, telefono_cliente, metodo_pago, referencia, monto) 
            VALUES ('$nombre', '$apellido', '$cedula', '$telefono', '$metodo', '$referencia', '$monto')";

    if (mysqli_query($conn, $sql)) {
        // Éxito: Saltamos a la factura PDF
        header("Location: factura.php");
        exit();
    } else {
        echo "Error en el pedido: " . mysqli_error($conn);
    }
}
?>