<?php
include("connection.php");

if (isset($_POST['agregar_producto'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    // Datos de la imagen
    $nombre_foto = $_FILES['foto']['name'];
    $ruta_temporal = $_FILES['foto']['tmp_name'];
    
    // Creamos un nombre único para la foto (evita que se sobrescriban si se llaman igual)
    $extension = pathinfo($nombre_foto, PATHINFO_EXTENSION);
    $nuevo_nombre_foto = time() . "_" . str_replace(' ', '_', $nombre) . "." . $extension;
    $ruta_destino = "img/" . $nuevo_nombre_foto;

    // 1. Movemos el archivo físico a la carpeta img
    if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
        
        // 2. Insertamos en la base de datos
        $sql = "INSERT INTO productos_finales (nombre, precio, imagen) 
                VALUES ('$nombre', '$precio', '$nuevo_nombre_foto')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>
                    alert('¡Producto y foto guardados con éxito!');
                    window.location.href = 'admin.php'; 
                  </script>";
        } else {
            echo "Error en BD: " . mysqli_error($conn);
        }

    } else {
        echo "Error al subir la imagen. Revisa los permisos de la carpeta img.";
    }
}
?>