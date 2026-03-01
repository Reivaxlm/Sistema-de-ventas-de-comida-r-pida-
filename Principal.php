<?php
require('connection.php');
session_start();

if(!isset($_SESSION['id'])){ 
    header("Location: index.html"); 
    exit(); 
}

$id = $_SESSION['id'];
$nombre_sesion = $_SESSION['username'] ?? 'Vendedor';

$sql = "SELECT * FROM perfil WHERE id_usuario = '$id'";
$res = mysqli_query($conn, $sql);
$mostrar = mysqli_fetch_array($res);

if ($mostrar && !empty($mostrar['imagen'])) {
    $img_vendedor = $mostrar['imagen'];
} else {
    $img_vendedor = 'imgs/Man.jpg'; 
}

// CONFIGURACIÓN VENEZUELA
date_default_timezone_set('America/Caracas'); 
$hoy = date('Y-m-d');

// 1. Total del Día (Usamos LIKE para asegurar que detecte la fecha en Venezuela)
$query_total = mysqli_query($conn, "SELECT SUM(monto) as gran_total FROM cliente WHERE reg_date LIKE '$hoy%'");
$dato_total = mysqli_fetch_assoc($query_total);
$monto_total_dia = $dato_total['gran_total'] ?? 0;

// 2. Conteo de Ventas
$query_conteo = mysqli_query($conn, "SELECT COUNT(*) as total FROM cliente WHERE reg_date LIKE '$hoy%'");
$v_data = mysqli_fetch_assoc($query_conteo);
$total_ventas_conteo = $v_data['total'] ?? 0;

// 3. RECUPERAR HISTORIAL (Últimos 3 movimientos)
$query_historial = mysqli_query($conn, "SELECT monto, reg_date FROM cliente ORDER BY id DESC LIMIT 3");

$meta_del_dia = 500.00; 
$porcentaje_meta = ($monto_total_dia / $meta_del_dia) * 100;
$ancho_barra = ($porcentaje_meta > 100) ? 100 : $porcentaje_meta;

$pedidos_cola = mysqli_query($conn, "SELECT * FROM cliente WHERE DATE(reg_date) = CURDATE() AND estado != 'entregado' ORDER BY reg_date ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Burger Designers | Panel</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="body-dashboard">

<nav class="nav">
    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="font-weight: 900; letter-spacing: -1px;">BURGER <span style="color: #fcc404;">DESIGNERS</span></h3>
    </div>

    <div style="display: flex; flex-direction: column; gap: 5px;">
        <a href="Principal.php" class="nav-link" style="background: #f0f0f0; border: 2px solid #000;">🏠 INICIO</a>
        <a href="pedido.php" class="nav-link">🍔 NUEVO PEDIDO</a>
    </div>

    <div class="seccion-header">Caja de Hoy</div>
    <div class="card-total-compact">
        <small>Total Acumulado</small>
        <h2>$<?php echo number_format($monto_total_dia, 2); ?></h2>
        <div class="badge-ventas">
            <?php echo $total_ventas_conteo; ?> VENTAS HOY
        </div>
    </div>

    <div class="seccion-header">Últimos Movimientos</div>
    <div style="max-height: 150px; overflow-y: auto;">
        <?php 
        // Aseguramos que la variable exista antes de usarla
        if(isset($query_historial) && mysqli_num_rows($query_historial) > 0):
            mysqli_data_seek($query_historial, 0); 
            while($reg = mysqli_fetch_assoc($query_historial)): ?>
                <div class="item-brutalista item-historial">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <b style="font-size: 14px;">$<?php echo number_format($reg['monto'], 2); ?></b>
                        <small style="font-size: 10px; color: #666;"><?php echo date('H:i', strtotime($reg['reg_date'])); ?></small>
                    </div>
                </div>
            <?php endwhile; 
        endif; ?>
    </div>

    <div class="seccion-header">Pedidos Activos</div>
    <div style="flex: 1; overflow-y: auto;">
        <?php mysqli_data_seek($pedidos_cola, 0); ?>
        <?php while($pedido = mysqli_fetch_assoc($pedidos_cola)): 
            $status_color = ($pedido['estado'] == 'preparando') ? "#dd1919" : (($pedido['estado'] == 'listo') ? "#28a745" : "#fcc404");
        ?>
            <div class="item-brutalista">
                <div style="margin-bottom: 5px;">
                    <b style="font-size: 12px;"><?php echo strtoupper($pedido['nombre_cliente']); ?></b>
                </div>
                <div style="font-size: 20px; font-weight: 900; margin-bottom: 10px;">$<?php echo number_format($pedido['monto'], 2); ?></div>
                
                <select onchange="actualizarEstado(<?php echo $pedido['id']; ?>, this.value)" 
                    style="width:100%; border:2px solid #000; font-weight:900; border-radius:5px;">
                    <option value="pendiente" <?php if($pedido['estado'] == 'pendiente') echo 'selected'; ?>>PENDIENTE</option>
                    <option value="preparando" <?php if($pedido['estado'] == 'preparando') echo 'selected'; ?>>COCINANDO</option>
                    <option value="listo" <?php if($pedido['estado'] == 'listo') echo 'selected'; ?>>LISTO</option>
                    <option value="entregado">ENTREGADO</option>
                </select>
            </div>
        <?php endwhile; ?>
    </div>

    <a href="index.html" class="nav-link" style="margin-top: 20px; background: #000; color: #fff; text-align: center; border: 3px solid #000; box-shadow: 4px 4px 0px #dd1919;">
        CERRAR SESIÓN
    </a>
</nav>
<main class="main-content">
    <div class="perfil-completo">
        <div style="display: flex; align-items: stretch; gap: 15px; margin-bottom: 20px;">
            <div style="width: 70px; background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; border: 1px solid #eee;">
                <a href="vendedor.html" style="text-decoration: none; background: #f9f9f9; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #ddd;" title="Configurar Perfil">
                    <span style="font-size: 20px;">⚙️</span>
                </a>
            </div>

            <div class="header-perfil" style="flex: 1; margin: 0; display: flex; align-items: center; background: #fff; padding: 20px; border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #eee;">
                <div class="avatar-container" style="margin: 0;">
                    <img src="<?php echo $img_vendedor; ?>" class="avatar-grande" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #fcc404;">
                </div>
                <div style="margin-left: 20px;">
                    <h1 class="nombre-vendedor" style="margin: 0; font-size: 24px; color: #333;"><?php echo $mostrar['nombre'] ?? $nombre_sesion; ?></h1>
                    <div class="badge-status" style="margin-top: 5px;">
                        <span class="id-tag" style="background:#000; color:#fff; padding:3px 8px; border-radius:4px; font-size: 11px;">ID #<?php echo $id; ?></span>
                        <span class="turno-tag" style="color:#2e7d32; font-weight:bold; font-size: 13px; margin-left: 10px;"> ● TURNO ACTIVO</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3>HISTORIAL / DESCRIPCIÓN</h3>
            <p class="Infox"><?php echo nl2br(htmlspecialchars($mostrar['descripcion'] ?? 'Vendedor listo para la jornada.')); ?></p>
        </div>

        <div class="card" style="margin-top: 20px; border: 2px solid #000; background: #fff; border-radius: 8px; overflow: hidden;">
            <h4 style="text-align: center; background: #000; color: #fff; padding: 10px; margin: 0;">📊 DISPONIBILIDAD DE INGREDIENTES</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 15px;">
                <?php
                $res_stock = mysqli_query($conn, "SELECT * FROM inventario");
                while($inv = mysqli_fetch_assoc($res_stock)){
                    $alerta = ($inv['cantidad'] <= 10) ? "color: red; font-weight: bold;" : "color: green;";
                    echo "<div style='border-bottom: 1px solid #eee; padding: 8px; display: flex; justify-content: space-between;'>
                            <span style='text-transform: capitalize;'>{$inv['insumo']}:</span> 
                            <span style='$alerta'>{$inv['cantidad']}</span>
                          </div>";
                }
                ?>
            </div>
        </div>
        <div class="card-metas" style="background: #fff; padding: 20px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h4 style="margin-bottom: 10px; color: #333;">🎯 Meta Diaria: <span style="float: right;">$<?php echo $monto_total_dia; ?> / $<?php echo $meta_del_dia; ?></span></h4>
            
            <div style="background: #eee; border-radius: 20px; height: 25px; width: 100%; overflow: hidden; border: 1px solid #ddd;">
                <div style="width: <?php echo $ancho_barra; ?>%; background: linear-gradient(90deg, #fcc404, #dd1919); height: 100%; transition: width 1s ease-in-out; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;">
                    <?php echo number_format($porcentaje_meta, 1); ?>%
                </div>
            </div>
            <p style="font-size: 12px; margin-top: 8px; color: #666;">
                <?php echo ($monto_total_dia >= $meta_del_dia) ? "¡Felicidades! Meta alcanzada 🚀" : "Faltan $" . ($meta_del_dia - $monto_total_dia) . " para el objetivo."; ?>
            </p>
    </div>
</main>

<script>
function actualizarEstado(idPedido, nuevoEstado) {
    // Usamos fetch para conectarnos con tu archivo PHP
    fetch('actualizar_estado.php', {
        method: 'POST',
        body: JSON.stringify({
            id: idPedido,
            estado: nuevoEstado
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargamos la página para que se vean los cambios (bordes de colores)
            // y para que si es "entregado" desaparezca de la lista
            location.reload(); 
        } else {
            alert('Error: No se pudo actualizar en la base de datos');
            console.error(data.error);
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
    });
}
</script>
</body>
</html>
