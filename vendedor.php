<?php
require('connection.php'); 
session_start(); // <--- ERROR 1 CORREGIDO: Necesario para saber quién eres

// Validar que el usuario esté logueado
if (!isset($_SESSION['id'])) {
    die("Error: No has iniciado sesión.");
}

if (isset($_POST['guardar'])) {
    $id_usuario = $_SESSION['id']; // <--- ESTO CONECTA EL PERFIL CON TU CUENTA
    
    $nombre = $_POST['nombre_vendedor'];
    $apellido = $_POST['apellido_vendedor'];
    $cedula = $_POST['cedula_vendedor'];
    $telefono = $_POST['telefono_vendedor'];
    $correo = $_POST['correo_vendedor'];
    $descripcion = $_POST['descripcion_vendedor'];

    $contenido_final = "";

    // --- (Tu código de procesamiento de imagen se mantiene igual) ---
    if (isset($_FILES['imagen']) && $_FILES['imagen']['tmp_name'] != "") {
        $ruta_temporal = $_FILES['imagen']['tmp_name'];
        $info = getimagesize($ruta_temporal);
        if ($info !== false) {
            $tipo_mime = $info['mime'];
            switch ($tipo_mime) {
                case 'image/jpeg': $fuente = imagecreatefromjpeg($ruta_temporal); break;
                case 'image/png':  $fuente = imagecreatefrompng($ruta_temporal); break;
                case 'image/webp': $fuente = imagecreatefromwebp($ruta_temporal); break;
                default: $fuente = null;
            }
            if ($fuente) {
                $lienzo = imagecreatetruecolor(500, 500);
                imagealphablending($lienzo, false);
                imagesavealpha($lienzo, true);
                imagecopyresampled($lienzo, $fuente, 0, 0, 0, 0, 500, 500, $info[0], $info[1]);
                ob_start();
                imagejpeg($lienzo, null, 85);
                $contenido_final = ob_get_clean();
                imagedestroy($fuente);
                imagedestroy($lienzo);
            }
        }
    }

    // ERROR 2 CORREGIDO: Cambiamos INSERT por REPLACE e incluimos el ID
    // Esto evita que se creen usuarios infinitos y actualiza el tuyo
    $query = "REPLACE INTO perfil (id, nombre_vendedor, apellido_vendedor, cedula_vendedor, telefono_vendedor, correo_vendedor, descripcion_vendedor, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $query);
    
    // Ahora son 8 parámetros (agregamos la "i" al principio para el ID entero)
    mysqli_stmt_bind_param($stmt, "isssssss", $id_usuario, $nombre, $apellido, $cedula, $telefono, $correo, $descripcion, $contenido_final);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Perfil actualizado con éxito'); window.location='Principal.php';</script>";
    } else {
        echo "Error al guardar: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>