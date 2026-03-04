<?php
session_start();
include('connection.php');

// 1. Verificar sesión
if(!isset($_SESSION['id'])){ 
    header("Location: index.html"); 
    exit(); 
}
$id_usuario = $_SESSION['id']; 

if (isset($_POST['guardar'])) {
    // CAPTURAMOS LOS CAMPOS QUE SÍ TIENES EN TU TABLA
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre_vendedor']);
    $cedula = mysqli_real_escape_string($conn, $_POST['cedula_vendedor']);
    $telefono = mysqli_real_escape_string($conn, $_POST['telefono_vendedor']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo_vendedor']);
    $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion_vendedor']);

    // 2. BUSCAR DATOS ACTUALES PARA MANTENER LA IMAGEN
    $consulta_actual = mysqli_query($conn, "SELECT imagen FROM perfil WHERE id_usuario = '$id_usuario'");
    $datos_perfil = mysqli_fetch_assoc($consulta_actual);
    
    // Si no hay imagen previa, ponemos una por defecto
    $ruta_imagen_final = ($datos_perfil && !empty($datos_perfil['imagen'])) ? $datos_perfil['imagen'] : 'imgs/Man.jpg'; 

    // 3. PROCESAR IMAGEN NUEVA SI EL USUARIO SUBIÓ UNA
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombre_archivo = $_FILES['imagen']['name'];
        $ruta_temporal = $_FILES['imagen']['tmp_name'];
        $extension = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
        $nuevo_nombre = time() . "_" . $id_usuario . "." . $extension;
        $destino = "imgs/" . $nuevo_nombre;

        if (move_uploaded_file($ruta_temporal, $destino)) {
            $ruta_imagen_final = $destino;
        }
    }

    // 4. GUARDAR O ACTUALIZAR SEGÚN TU ESTRUCTURA
    if (mysqli_num_rows($consulta_actual) > 0) {
        // ACTUALIZAR (Sin la columna apellido)
        $sql = "UPDATE perfil SET 
                nombre='$nombre', 
                cedula='$cedula',
                telefono='$telefono',
                correo='$correo',
                descripcion='$descripcion',
                imagen='$ruta_imagen_final' 
                WHERE id_usuario='$id_usuario'";
    } else {
        // INSERTAR (Sin la columna apellido)
        $sql = "INSERT INTO perfil (id_usuario, nombre, cedula, telefono, correo, descripcion, imagen) 
                VALUES ('$id_usuario', '$nombre', '$cedula', '$telefono', '$correo', '$descripcion', '$ruta_imagen_final')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('¡Perfil actualizado correctamente!'); window.location.href='Principal.php';</script>";
    } else {
        echo "Error en la base de datos: " . mysqli_error($conn);
    }
}
?>