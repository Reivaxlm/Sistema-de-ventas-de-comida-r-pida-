<?php
require('connection.php');
session_start();

// Verificación de sesión
if(!isset($_SESSION['id'])){ header("Location: index.html"); exit(); }

$id = $_SESSION['id'];
$nombre_sesion = $_SESSION['name'] ?? 'Vendedor';

// Consulta de perfil
$sql = "SELECT * FROM perfil WHERE id = '$id'";
$res = mysqli_query($conn, $sql);
$mostrar = mysqli_fetch_array($res);

// Lógica de imagen
if ($mostrar && !empty($mostrar['imagen'])) {
    $img_vendedor = 'data:image/jpeg;base64,' . base64_encode($mostrar['imagen']);
} else {
    $img_vendedor = 'imgs/Man.jpg'; 
}

// --- LÓGICA DE DATOS PARA LA BARRA LATERAL ---
$hoy = date('Y-m-d');

// 1. Monto Total del Día
$query_total = mysqli_query($conn, "SELECT SUM(total_price) as gran_total FROM pago WHERE DATE(reg_date) = '$hoy'");
$dato_total = mysqli_fetch_assoc($query_total);
$monto_total_dia = $dato_total['gran_total'] ?? 0;

// 2. Historial de últimas 3 ventas
$query_historial = mysqli_query($conn, "SELECT total_price, reg_date FROM pago ORDER BY id DESC LIMIT 3");

// 3. Conteo de atenciones
$ventas_vendedor = mysqli_query($conn, "SELECT COUNT(*) as total FROM pago WHERE DATE(reg_date) = '$hoy'");
$v_data = mysqli_fetch_assoc($ventas_vendedor);
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
                    <b style="color: #fff; display: block; font-size: 13px;">Monto: $<?php echo number_format($reg['total_price'], 2); ?></b>
                    <small style="color: #666; font-size: 10px;"><?php echo date('H:i A', strtotime($reg['reg_date'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <li style="padding: 20px 20px 5px; font-size: 11px; color: #999; text-transform: uppercase;">Avisos del Admin</li>
        <div style="padding: 0 20px;">
            <div style="background: #fffbe6; border: 1px solid #ffe58f; padding: 10px; border-radius: 8px; font-size: 12px; color: #856404;">
                📌 <b>Nota:</b> Limpiar plancha cada 2 horas y revisar fecha de vencimiento de quesos.
            </div>
        </div>

        <li style="margin-top: 30px;" class="list__item">
            <div class="list__button">
                <a href="logout.php" class="nav__link" style="color:#dd1919; font-weight:bold;">🔴 Cerrar Sesion</a>
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
                    <h1 class="nombre-vendedor" style="margin: 0; font-size: 24px; color: #333;"><?php echo $mostrar['nombre_vendedor'] ?? $nombre_sesion; ?></h1>
                    <div class="badge-status" style="margin-top: 5px;">
                        <span class="id-tag" style="background:#000; color:#fff; padding:3px 8px; border-radius:4px; font-size: 11px;">ID #<?php echo $id; ?></span>
                        <span class="turno-tag" style="color:#2e7d32; font-weight:bold; font-size: 13px; margin-left: 10px;"> ● TURNO ACTIVO</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h3>HISTORIAL / DESCRIPCIÓN</h3>
            <p class="Infox"><?php echo $mostrar['descripcion_vendedor'] ?? 'Vendedor listo para la jornada.'; ?></p>
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

        <div class="cierre-container" style="margin-top: 30px;">
            <a href="logout.php" class="btn-regresar-impacto" style="background:#dd1919; color:#fff !important; width: 100%; justify-content: center; text-decoration: none; display: flex; align-items: center; padding: 15px; border-radius: 10px;">
                <span style="background:#000; padding: 5px 10px; border-radius: 5px; margin-right: 10px;">💰</span> 
                <b>FINALIZAR JORNADA Y CERRAR</b>
            </a>
        </div>
    </div>
</main>

<script>
    function actualizarReloj() {
        const ahora = new Date();
        document.getElementById('reloj-vendedor').textContent = ahora.toLocaleTimeString();
    }
    setInterval(actualizarReloj, 1000);
    actualizarReloj();
</script>
</body>
</html>