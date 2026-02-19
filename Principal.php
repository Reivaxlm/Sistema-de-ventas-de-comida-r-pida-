<?php
require('connection.php');
session_start();

if(!isset($_SESSION['id'])){
    header("Location: index.html");
    exit();
}

$id = $_SESSION['id'];

// Consulta para los recuadros del perfil
$sql = "SELECT * FROM perfil WHERE id = '$id'";
$result = mysqli_query($conn, $sql);
$mostrar = mysqli_fetch_array($result);

if(!$mostrar){
    $mostrar = [
        'nombre_vendedor' => 'Vendedor', 'apellido_vendedor' => 'Nuevo',
        'cedula_vendedor' => '---', 'telefono_vendedor' => '---',
        'descripcion_vendedor' => 'Completa tu perfil.', 'imagen' => null
    ];
}
$imagen_codificada = ($mostrar['imagen']) ? base64_encode($mostrar['imagen']) : "";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Ventas - Inicio</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilo rápido para asegurar el despliegue */
        .list__show {
            list-style: none;
            transition: height .3s;
            overflow: hidden;
        }
    </style>
</head>
<body class="body-dashboard">

    <nav class="nav">
        <div class="nav__logo">
            <h3 class="nav__title">Panel Control</h3>
        </div>

        <ul class="list">
            <li class="list__item">
                <div class="list__button">
                    <a href="Principal.php" class="nav__link">Inicio</a>
                </div>
            </li>

            <li class="list__item list__item--click">
                <div class="list__button list__button--click" onclick="toggleMenu()">
                    <a href="#" class="nav__link">Ventas</a>
                    </div>

                <ul class="list__show" id="menuVentas" style="height: 0px;">
                    <li class="list__inside">
                        <a href="pedido.html" class="nav__link nav__link--inside">Hacer Pedido</a>
                    </li>
                    <li class="list__inside">
                        <a href="reportecliente.php" class="nav__link nav__link--inside">Ver Reportes</a>
                    </li>
                </ul>
            </li>

            <li class="list__item">
                <div class="list__button">
                    <a href="index.html" class="nav__link">Cerrar Sesión</a>
                </div>
            </li>
        </ul>
    </nav>

    <main class="main-content">
        <div class="perfil-card">
            <div class="perfil-header">
                <div class="avatar-view">
                    <?php if($imagen_codificada): ?>
                        <img src="data:image/jpeg;base64,<?php echo $imagen_codificada; ?>" class="avatar-img">
                    <?php else: ?>
                        <div class="avatar-placeholder"></div>
                    <?php endif; ?>
                </div>
                <h2><?php echo $mostrar['nombre_vendedor'] . " " . $mostrar['apellido_vendedor']; ?></h2>
            </div>

            <div class="perfil-body">
                <div class="info-item"><strong>Cédula:</strong> <?php echo $mostrar['cedula_vendedor']; ?></div>
                <div class="info-item"><strong>Teléfono:</strong> <?php echo $mostrar['telefono_vendedor']; ?></div>
                <div class="info-item"><strong>Bio:</strong> <?php echo $mostrar['descripcion_vendedor']; ?></div>
            </div>
            
            <a href="vendedor.html" class="btn-edit">Editar mi Perfil</a>
        </div>
    </main>

    <script>
        /* SCRIPT PARA EL DESPLIEGUE LATERAL */
        function toggleMenu() {
            const menu = document.getElementById("menuVentas");
            if (menu.style.height === "0px" || menu.style.height === "") {
                menu.style.height = menu.scrollHeight + "px";
            } else {
                menu.style.height = "0px";
            }
        }
    </script>
</body>
</html>