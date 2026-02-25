<?php
require('connection.php');
session_start();

// Verificación de sesión
if(!isset($_SESSION['id'])){ 
    header("Location: index.html"); 
    exit(); 
}

$id = $_SESSION['id'];
$nombre_sesion = $_SESSION['username'] ?? 'Vendedor'; // Usamos el username de registro si no hay perfil

// 1. CONSULTA DE PERFIL CORREGIDA
// Buscamos en la tabla perfil donde el id_usuario coincida con el de la sesión
$sql = "SELECT * FROM perfil WHERE id_usuario = '$id'";
$res = mysqli_query($conn, $sql);
$mostrar = mysqli_fetch_array($res);

// 2. LÓGICA DE IMAGEN MEJORADA
// Primero verificamos si existe un registro y si tiene una ruta de imagen
if ($mostrar && !empty($mostrar['imagen'])) {
    // Si la imagen guardada ya es una ruta (ej: imgs/foto.jpg), la usamos directamente
    $img_vendedor = $mostrar['imagen'];
} else {
    // Si no hay nada, imagen por defecto
    $img_vendedor = 'imgs/Man.jpg'; 
}

// --- LÓGICA DE DATOS PARA LA BARRA LATERAL ---
$hoy = date('Y-m-d');

// 1. Monto Total del Día (Sumamos de la tabla cliente que es donde guardas las ventas finales)
$query_total = mysqli_query($conn, "SELECT SUM(monto) as gran_total FROM cliente WHERE DATE(reg_date) = '$hoy'");
$dato_total = mysqli_fetch_assoc($query_total);
$monto_total_dia = $dato_total['gran_total'] ?? 0;

// 2. Historial de últimas 3 ventas
$query_historial = mysqli_query($conn, "SELECT monto, reg_date FROM cliente ORDER BY id DESC LIMIT 3");

// 3. Conteo de atenciones
$ventas_vendedor = mysqli_query($conn, "SELECT COUNT(*) as total FROM cliente WHERE DATE(reg_date) = '$hoy'");
$v_data = mysqli_fetch_assoc($ventas_vendedor);

// Definimos la meta del día
$meta_del_dia = 500.00; 
$porcentaje_meta = ($monto_total_dia / $meta_del_dia) * 100;

// Evitar que la barra pase del 100% visualmente
$ancho_barra = ($porcentaje_meta > 100) ? 100 : $porcentaje_meta;

$pedidos_cola = mysqli_query($conn, "SELECT * FROM cliente WHERE DATE(reg_date) = CURDATE() AND estado != 'entregado' ORDER BY reg_date ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Burger Designers | Panel Vendedor</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="body-dashboard">
<nav class="nav">
    <div class="nav__logo"><h3>BURGER DESIGNERS</h3></div>
    <ul class="list">
        <li class="list__item">
            <div class="list__button">
                <img src="assets/dashboard.svg" class="list__img">
                <a href="Principal.php" class="nav__link">Inicio / Panel</a>
            </div>
        </li>
        <li class="list__item">
            <div class="list__button">
                <img src="assets/docs.svg" class="list__img">
                <a href="pedido.php" class="nav__link">Hacer Pedido</a>
            </div>
        </li>

        <li style="padding: 20px 20px 5px; font-size: 11px; color: #fcc404; text-transform: uppercase;">Balance de Hoy</li>
        <div style="padding: 0 20px;">
            <div style="background: #000; padding: 15px; border-radius: 10px; border: 1px solid #333; color: #fff;">
                <span style="color: #fcc404; font-size: 11px; display: block; margin-bottom: 5px;">TOTAL VENDIDO:</span>
                <h2 style="margin: 0; font-size: 22px;">$<?php echo number_format($monto_total_dia, 2); ?></h2>
                <small style="color: #888;"><?php echo $v_data['total']; ?> ventas hoy</small>
            </div>
        </div>

        <li style="padding: 20px 20px 5px; font-size: 11px; color: #999; text-transform: uppercase;">Últimas Ventas</li>
        <div style="padding: 0 20px;">
            <?php while($reg = mysqli_fetch_assoc($query_historial)): ?>
                <div style="border-left: 3px solid #fcc404; padding-left: 10px; margin-bottom: 10px; background: rgba(0,0,0,0.3); padding: 5px 10px; border-radius: 0 5px 5px 0;">
                    <b style="color: #fff; display: block; font-size: 13px;">Monto: $<?php echo number_format($reg['monto'], 2); ?></b>
                    <small style="color: #666; font-size: 10px;"><?php echo date('H:i A', strtotime($reg['reg_date'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="cola-pedidos" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            <?php while($pedido = mysqli_fetch_assoc($pedidos_cola)): ?>
                <div class="ticket-pedido" style="background: #fff; border-left: 5px solid #fcc404; padding: 15px; border-radius: 8px; position: relative;">
                    <span style="font-size: 10px; color: #999;"><?php echo date('H:i', strtotime($pedido['reg_date'])); ?></span>
                    <h5 style="margin: 5px 0;"><?php echo $pedido['nombre_cliente']; ?></h5>
                    <p style="font-size: 12px; color: #555;">Monto: $<?php echo $pedido['monto']; ?></p>
                    
                    <div style="margin-top: 10px;">
                        <select onchange="actualizarEstado(<?php echo $pedido['id']; ?>, this.value)" style="width: 100%; padding: 5px; border-radius: 5px; border: 1px solid #ccc; font-size: 12px;">
                            <option value="pendiente">Pendiente ⏳</option>
                            <option value="preparando">En Cocina 🔥</option>
                            <option value="listo">Listo ✅</option>
                            <option value="entregado">Entregado 🏁</option>
                        </select>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div style="padding: 20px;">
            <h3 style="margin-bottom: 15px;">🍔 Pedidos en Cola (Pendientes)</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                
                <?php
                // Consultamos solo los pedidos de hoy que no estén 'entregados'
                $query_cola = mysqli_query($conn, "SELECT * FROM cliente WHERE DATE(reg_date) = CURDATE() AND (estado != 'entregado' OR estado IS NULL) ORDER BY reg_date ASC");
                
                while($pedido = mysqli_fetch_assoc($query_cola)):
                    // Definimos color según estado
                    $color_borde = "#fcc404"; // Pendiente
                    if($pedido['estado'] == 'preparando') $color_borde = "#dd1919"; // Rojo
                    if($pedido['estado'] == 'listo') $color_borde = "#28a745"; // Verde
                ?>
                
                <div id="card-<?php echo $pedido['id']; ?>" style="background: white; border-left: 8px solid <?php echo $color_borde; ?>; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between;">
                        <strong>#<?php echo $pedido['id']; ?> - <?php echo $pedido['nombre_cliente']; ?></strong>
                        <span style="font-size: 12px; color: #888;"><?php echo date('H:i', strtotime($pedido['reg_date'])); ?></span>
                    </div>
                    <p style="font-size: 13px; color: #666; margin: 5px 0;">Monto: $<?php echo $pedido['monto']; ?></p>
                    
                    <select onchange="cambiarEstado(<?php echo $pedido['id']; ?>, this.value)" style="width: 100%; padding: 8px; border-radius: 5px; margin-top: 10px; cursor: pointer;">
                        <option value="pendiente" <?php if($pedido['estado'] == 'pendiente') echo 'selected'; ?>>⏳ Pendiente</option>
                        <option value="preparando" <?php if($pedido['estado'] == 'preparando') echo 'selected'; ?>>🔥 Preparando</option>
                        <option value="listo" <?php if($pedido['estado'] == 'listo') echo 'selected'; ?>>✅ Listo para retirar</option>
                        <option value="entregado">🏁 Entregado (Quitar de lista)</option>
                    </select>
                </div>

                <?php endwhile; ?>
            </div>
        </div>

        <li style="margin-top: 30px;" class="list__item">
            <div class="list__button">
                <a href="login.html" class="nav__link" style="color:#dd1919; font-weight:bold;">🔴 Cerrar Sesion</a>
            </div>
        </li>
    </ul>
    <div id="reloj-vendedor" style="text-align: center; color: #fff; padding: 15px; font-weight: bold; font-family: monospace; font-size: 14px;">00:00:00</div>
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
    fetch('actualizar_estado_pedido.php', {
        method: 'POST',
        body: JSON.stringify({id: idPedido, estado: nuevoEstado}),
        headers: {'Content-Type': 'application/json'}
    }).then(res => {
        if(nuevoEstado === 'entregado') {
            location.reload(); // Recargamos para que desaparezca de la cola
        }
    });
}
</script>
</body>
</html>