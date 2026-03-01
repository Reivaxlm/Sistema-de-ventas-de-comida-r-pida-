<?php
require('connection.php');

// Leemos los datos enviados por el JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['estado'])) {
    $id = mysqli_real_escape_string($conn, $data['id']);
    $estado = mysqli_real_escape_string($conn, $data['estado']);

    // Actualizamos la tabla cliente
    $sql = "UPDATE cliente SET estado = '$estado' WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}
?>