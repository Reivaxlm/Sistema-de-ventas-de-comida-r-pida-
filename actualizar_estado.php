<?php
include('connection.php');

// Recibimos los datos enviados por el JavaScript (AJAX)
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['estado'])) {
    $id = $data['id'];
    $estado = $data['estado'];

    // Actualizamos el estado en la tabla cliente
    $sql = "UPDATE cliente SET estado = '$estado' WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>