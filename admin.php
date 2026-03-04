<?php
include("connection.php");

// --- 1. LÓGICA DE ORDENACIÓN PARA LAS TABLAS ---
$sort_ventas = "id DESC"; 
if (isset($_GET['sort_v'])) {
    if ($_GET['sort_v'] == 'm_asc') $sort_ventas = "monto ASC";
    if ($_GET['sort_v'] == 'm_desc') $sort_ventas = "monto DESC";
    if ($_GET['sort_v'] == 'f_asc') $sort_ventas = "reg_date ASC";
    if ($_GET['sort_v'] == 'f_desc') $sort_ventas = "reg_date DESC";
}

// --- 2. CONSULTAS DE INTELIGENCIA DE NEGOCIO ---

// A. Ventas Hoy vs Ayer
$res_hoy = mysqli_query($conn, "SELECT SUM(monto) as total FROM cliente WHERE DATE(reg_date) = CURDATE()");
$total_hoy = mysqli_fetch_assoc($res_hoy)['total'] ?? 0;

$res_ayer = mysqli_query($conn, "SELECT SUM(monto) as total FROM cliente WHERE DATE(reg_date) = SUBDATE(CURDATE(),1)");
$total_ayer = mysqli_fetch_assoc($res_ayer)['total'] ?? 0;
$dif_ayer = $total_hoy - $total_ayer;

// B. Ticket Promedio (Gasto por cliente)
$res_avg = mysqli_query($conn, "SELECT AVG(monto) as promedio FROM cliente");
$promedio = mysqli_fetch_assoc($res_avg)['promedio'] ?? 0;

// C. Hora Pico (A qué hora cae más dinero)
$res_hora = mysqli_query($conn, "SELECT HOUR(reg_date) as hora, COUNT(*) as cant FROM cliente GROUP BY hora ORDER BY cant DESC LIMIT 1");
$hora_data = mysqli_fetch_assoc($res_hora);
$h_pico = $hora_data['hora'] ?? 0;
$formato_hora = ($h_pico >= 12) ? ($h_pico == 12 ? 12 : $h_pico-12)." PM" : ($h_pico == 0 ? 12 : $h_pico)." AM";

// D. Ranking: Más Vendido (Por monto de combo)
$res_top = mysqli_query($conn, "SELECT monto, COUNT(*) as cantidad FROM cliente GROUP BY monto ORDER BY cantidad DESC LIMIT 3");

// E. Datos para Gráfico de Líneas (7 días)
$res_sem = mysqli_query($conn, "SELECT DATE(reg_date) as dia, SUM(monto) as total FROM cliente GROUP BY dia ORDER BY dia ASC LIMIT 7");
$lab_sem = []; $dat_sem = [];
while($f = mysqli_fetch_assoc($res_sem)){ $lab_sem[] = date("d M", strtotime($f['dia'])); $dat_sem[] = $f['total']; }

// F. Datos para Gráfico Circular (Métodos)
$res_met = mysqli_query($conn, "SELECT metodo_pago, COUNT(*) as cant FROM cliente GROUP BY metodo_pago");
$lab_met = []; $dat_met = [];
while($m = mysqli_fetch_assoc($res_met)){ $lab_met[] = $m['metodo_pago']; $dat_met[] = $m['cant']; }

// --- LÓGICA DE GESTIÓN DE PERSONAL ---
// A. Borrar Usuario
if (isset($_GET['delete_user'])) {
    $id_del = intval($_GET['delete_user']);
    mysqli_query($conn, "DELETE FROM registro WHERE id = $id_del");
    echo "<script>alert('Usuario eliminado'); window.location.href='admin.php';</script>";
}

// B. Editar Usuario
if (isset($_POST['btn_update_user'])) {
    $id_upd = $_POST['id_usuario'];
    $nom = mysqli_real_escape_string($conn, $_POST['nombre']);
    $usr = mysqli_real_escape_string($conn, $_POST['usuario']);
    
    // Si escribes una clave, se cambia; si no, queda la anterior
    if (!empty($_POST['password'])) {
        $pass = $_POST['password'];
        $sql = "UPDATE registro SET name='$nom', username='$usr', password='$pass' WHERE id=$id_upd";
    } else {
        $sql = "UPDATE registro SET name='$nom', username='$usr' WHERE id=$id_upd";
    }
    
    mysqli_query($conn, $sql);
    echo "<script>alert('Datos actualizados'); window.location.href='admin.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Maestro | Burger Designers</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="wrapper">
    <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>BURGER DESIGNERS <span style="color:#dd1919;">ADMIN</span> 🍔</h1>
        <div style="display: flex; gap: 10px;">
            <a href="descargar_excel.php" class="btn-excel">📊 EXPORTAR EXCEL</a>
            <button onclick="window.location.href='index.html'" class="btn-excel" style="background:#000;">CERRAR SESIÓN</button>
        </div>
    </header>

    <div class="grid-main">
        <div class="card card-yellow">
            <span style="font-weight:bold;">💰 VENTAS HOY</span>
            <h2 style="font-size: 32px; margin: 10px 0;">$<?php echo number_format($total_hoy, 2); ?></h2>
            <span class="trend" style="<?php echo ($dif_ayer >= 0) ? 'color:green' : 'color:red'; ?>">
                <?php echo ($dif_ayer >= 0) ? '▲' : '▼'; ?> $<?php echo number_format(abs($dif_ayer), 2); ?> vs ayer
            </span>
        </div>
        <div class="card">
            <span style="font-weight:bold; color: #666;">📈 TICKET PROMEDIO</span>
            <h2 style="font-size: 32px; margin: 10px 0;">$<?php echo number_format($promedio, 2); ?></h2>
            <span style="font-size:12px;">Gasto medio por pedido</span>
        </div>
        <div class="card card-dark">
            <span style="font-weight:bold; color: #fcc404;">⏰ HORA PICO</span>
            <h2 style="font-size: 32px; margin: 10px 0;"><?php echo $formato_hora; ?></h2>
            <span style="font-size:12px;">Mayor volumen de clientes</span>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; margin-bottom: 25px;">
        <div class="card">
            <h3>📈 Rendimiento de la Semana</h3>
            <canvas id="lineChart" height="120"></canvas>
        </div>
        <div class="card">
            <h3>🏆 Ranking: Combos más pedidos</h3>
            <div style="margin-top: 15px;">
                <?php $i=1; while($top = mysqli_fetch_assoc($res_top)): ?>
                <div style="display:flex; justify-content:space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                    <span><span class="badge">#<?php echo $i++; ?></span> Combo de $<?php echo number_format($top['monto'], 2); ?></span>
                    <strong><?php echo $top['cantidad']; ?> Pedidos</strong>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
        <div class="card">
            <h3>💳 Métodos de Pago</h3>
            <canvas id="pieChart"></canvas>
        </div>
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3>📑 Últimas Ventas</h3>
                <a href="reportes_completos.php" style="font-size:12px; color: #dd1919; font-weight:bold;">VER TODO →</a>
            </div>
            <table>
                <tr>
                    <th>Fecha <a href="?sort_v=f_asc">▲</a><a href="?sort_v=f_desc">▼</a></th>
                    <th>Cliente</th>
                    <th>Monto <a href="?sort_v=m_asc">▲</a><a href="?sort_v=m_desc">▼</a></th>
                    <th>Ref</th>
                </tr>
                <?php 
                $res_v = mysqli_query($conn, "SELECT * FROM cliente ORDER BY $sort_ventas LIMIT 5");
                while($v = mysqli_fetch_assoc($res_v)): ?>
                <tr>
                    <td><?php echo date('d/m/y', strtotime($v['reg_date'])); ?></td>
                    <td><?php echo $v['nombre_cliente']; ?></td>
                    <td style="font-weight:bold; color:#dd1919;">$<?php echo number_format($v['monto'], 2); ?></td>
                    <td><code style="background:#eee; padding:2px;"><?php echo $v['referencia']; ?></code></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <div class="table-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2>👤 Gestión de Personal (Admin)</h2>
            <a href="register.html" class="btn-excel" style="background:#000; font-size:12px;">+ REGISTRAR NUEVO</a>
        </div>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
            <?php 
            $res_u = mysqli_query($conn, "SELECT * FROM registro ORDER BY id DESC");
            while($u = mysqli_fetch_assoc($res_u)): ?>
            <tr>
                <td>#<?php echo $u['id']; ?></td>
                <td><strong><?php echo $u['name']; ?></strong></td>
                <td><?php echo $u['username']; ?></td>
                <td>
                    <button onclick='abrirModalEditar(<?php echo json_encode($u); ?>)' style="background:none; border:none; color:blue; cursor:pointer; font-weight:bold;">Editar</button> | 
                    <a href="?delete_user=<?php echo $u['id']; ?>" onclick="return confirm('¿Eliminar a <?php echo $u['name']; ?>?')" style="color: red; text-decoration:none; font-weight:bold;">Borrar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 25px;">
        
        <div class="card">
            <h3>🍔 Configurar Nuevo Producto</h3>
            <form action="guardar_produccion.php" method="POST" enctype="multipart/form-data">
              <div class="input-group">
                  <label>Nombre de la Hamburguesa:</label>
                  <input type="text" name="nombre" required>
              </div>
          
              <div class="input-group">
                  <label>Precio:</label>
                  <input type="number" name="precio" required>
              </div>
          
              <div class="input-group">
                  <label>Imagen del Producto:</label>
                  <input type="file" name="foto" accept="image/*" required>
              </div>
          
              <button type="submit" name="agregar_producto">Guardar Producto</button>
          </form>

            <hr style="margin: 20px 0; border: 1px dashed #000;">

            <h3>📝 Armar Receta</h3>
            <form action="guardar_produccion.php" method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                <select name="id_producto" required style="padding: 8px; border: 2px solid #000; border-radius: 5px;">
                    <option value="">Seleccionar Producto...</option>
                    <?php 
                    $prods = mysqli_query($conn, "SELECT * FROM productos_finales");
                    while($p = mysqli_fetch_assoc($prods)) echo "<option value='{$p['id']}'>{$p['nombre']}</option>";
                    ?>
                </select>
                <select name="id_insumo" required style="padding: 8px; border: 2px solid #000; border-radius: 5px;">
                    <option value="">Seleccionar Ingrediente...</option>
                    <?php 
                    $insu = mysqli_query($conn, "SELECT * FROM inventario");
                    while($i = mysqli_fetch_assoc($insu)) echo "<option value='{$i['id']}'>{$i['insumo']}</option>";
                    ?>
                </select>
                <input type="number" name="cant_receta" placeholder="Cantidad que consume" step="0.01" required style="padding: 8px; border: 2px solid #000; border-radius: 5px;">
                <button type="submit" name="btn_receta" class="btn-excel" style="background: #000; color: #fff;">🔗 VINCULAR INGREDIENTE</button>
            </form>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3>📦 Inventario de Insumos</h3>
                <a href="agregar_insumo.php" class="badge" style="text-decoration:none;">+ NUEVO INSUMO</a>
            </div>
            <table>
                <tr>
                    <th>Ingrediente</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th> </tr>
                <?php 
                $res_inv = mysqli_query($conn, "SELECT * FROM inventario");
                while($inv = mysqli_fetch_assoc($res_inv)): 
                    $color_stock = ($inv['cantidad'] <= 10) ? 'red' : 'green';
                ?>
                <tr>
                    <td><?php echo $inv['insumo']; ?></td>
                    <td style="font-weight:bold;"><?php echo $inv['cantidad']; ?> <?php echo $inv['unidad']; ?></td>
                    <td><span style="color:<?php echo $color_stock; ?>;">● <?php echo ($inv['cantidad'] <= 10) ? 'Bajo' : 'Óptimo'; ?></span></td>
                    <td>
                        <a href="editar_insumo.php?id=<?php echo $inv['id']; ?>" style="color: blue; text-decoration:none; font-weight:bold;">✏️ Editar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</div>

<div id="modalPersonal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:999;">
    <div style="background:#fff; width:350px; margin:10% auto; padding:20px; border:5px solid #000; box-shadow:10px 10px 0px #fcc404;">
        <h2 style="margin-top:0;">EDITAR PERSONAL</h2>
        <form method="POST">
            <input type="hidden" name="id_usuario" id="edit_id">
            
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Nombre:</label>
            <input type="text" name="nombre" id="edit_nombre" required style="width:90%; padding:8px; border:3px solid #000; margin-bottom:15px;">
            
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Usuario:</label>
            <input type="text" name="usuario" id="edit_usuario" required style="width:90%; padding:8px; border:3px solid #000; margin-bottom:15px;">
            
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Contraseña (opcional):</label>
            <input type="password" name="password" placeholder="Dejar vacío para no cambiar" style="width:90%; padding:8px; border:3px solid #000; margin-bottom:20px;">
            
            <div style="display:flex; gap:10px;">
                <button type="submit" name="btn_update_user" class="btn-excel" style="background:#4caf50; flex:1;">GUARDAR</button>
                <button type="button" onclick="cerrarModal()" class="btn-excel" style="background:#666; flex:1;">CANCELAR</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalEditar(datos) {
    document.getElementById('edit_id').value = datos.id;
    document.getElementById('edit_nombre').value = datos.name;
    document.getElementById('edit_usuario').value = datos.username;
    document.getElementById('modalPersonal').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalPersonal').style.display = 'none';
}
</script>
<script>
// --- GRÁFICO DE LÍNEAS ---
const ctxLine = document.getElementById('lineChart').getContext('2d');
new Chart(ctxLine, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($lab_sem); ?>,
        datasets: [{
            label: 'Ventas USD',
            data: <?php echo json_encode($dat_sem); ?>,
            borderColor: '#dd1919',
            borderWidth: 4,
            backgroundColor: 'rgba(221, 25, 25, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { plugins: { legend: { display: false } } }
});

// --- GRÁFICO CIRCULAR ---
const ctxPie = document.getElementById('pieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($lab_met); ?>,
        datasets: [{
            data: <?php echo json_encode($dat_met); ?>,
            backgroundColor: ['#fcc404', '#1a1a1a', '#dd1919', '#666'],
            borderWidth: 2
        }]
    }
});

function eliminar(id) {
    if(confirm("¿Seguro que deseas eliminar al administrador ID: "+id+"?")) {
        window.location.href = "eliminar.php?id=" + id;
    }
}
</script>

</body>
</html>