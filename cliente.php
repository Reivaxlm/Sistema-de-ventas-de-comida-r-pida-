<?php
include 'connection.php';

$nombre = $_POST['nombre_cliente'];
$apellido = $_POST['apellido_cliente'];
$cedula = $_POST['cedula_cliente'];
$telefono = $_POST['telefono_cliente'];
$metodo_pago = $_POST['banco'];

$sql = "INSERT INTO cliente (nombre_cliente, apellido_cliente, cedula_cliente, telefono_cliente, metodo_pago) VALUES ('$nombre', '$apellido', '$cedula', '$telefono', '$metodo_pago')";

if (mysqli_query($conn, $sql)) {
  header('Location: factura.php');
 
} else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>