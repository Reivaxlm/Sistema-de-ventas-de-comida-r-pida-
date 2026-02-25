<?php
session_start();
include('connection.php');

// 1. Obtenemos el ID del usuario de la sesión
$id_usuario = $_SESSION['id']; 

if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre_vendedor'];
    // ... (tus otras variables de texto)

    // 2. BUSCAR LA IMAGEN ACTUAL EN LA BASE DE DATOS
    // Esto es vital para que si no suben nada, no se borre lo que ya existe
    $consulta_actual = mysqli_query($conn, "SELECT imagen FROM perfil WHERE id_usuario = '$id_usuario'");
    $datos_perfil = mysqli_fetch_assoc($consulta_actual);
    $ruta_imagen_final = $datos_perfil['imagen']; // Por defecto, dejamos la que ya estaba

    // 3. VERIFICAR SI SUBIERON UNA IMAGEN NUEVA
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombre_archivo = $_FILES['imagen']['name'];
        $ruta_temporal = $_FILES['imagen']['tmp_name'];
        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        
        // Creamos un nombre único para evitar duplicados
        $nuevo_nombre = time() . "_" . $id_usuario . "." . $extension;
        $destino = "imgs/" . $nuevo_nombre;

        // Intentamos mover el archivo
        if (move_uploaded_file($ruta_temporal, $destino)) {
            $ruta_imagen_final = $destino; // Solo si se subió con éxito, actualizamos la ruta
        }
    }

    // 4. ACTUALIZAR O INSERTAR EN LA BD
    // Usamos $ruta_imagen_final, que tendrá o la imagen nueva o la vieja
    if (mysqli_num_rows($consulta_actual) > 0) {
        $sql = "UPDATE perfil SET 
                nombre='$nombre', 
                imagen='$ruta_imagen_final' 
                WHERE id_usuario='$id_usuario'";
    } else {
        $sql = "INSERT INTO perfil (id_usuario, nombre, imagen) 
                VALUES ('$id_usuario', '$nombre', '$ruta_imagen_final')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('¡Perfil guardado!'); window.location.href='Principal.php';</script>";
    }
}
?>