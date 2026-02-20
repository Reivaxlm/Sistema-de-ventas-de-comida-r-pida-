<?php
include("connection.php");

// --- 1. CONSULTAS PARA LAS TARJETAS SUPERIORES ---
$res_hoy = mysqli_query($conn, "SELECT SUM(monto) as total FROM cliente WHERE DATE(reg_date) = CURDATE()");
$total_hoy = mysqli_fetch_assoc($res_hoy)['total'] ?? 0;

$res_mes = mysqli_query($conn, "SELECT SUM(monto) as total FROM cliente WHERE MONTH(reg_date) = MONTH(CURRENT_DATE())");
$total_mes = mysqli_fetch_assoc($res_mes)['total'] ?? 0;

// --- 2. CONSULTA PARA EL GRÁFICO DE LÍNEAS (Últimos 7 días) ---
$query_semana = "SELECT DATE(reg_date) as dia, SUM(monto) as total 
                 FROM cliente 
                 GROUP BY dia ORDER BY dia ASC LIMIT 7";
$res_semana = mysqli_query($conn, $query_semana);
$labels_semana = []; $datos_semana = [];
while($f = mysqli_fetch_assoc($res_semana)){
    $labels_semana[] = date("d M", strtotime($f['dia']));
    $datos_semana[] = $f['total'];
}

// --- 3. CONSULTA PARA EL GRÁFICO DE TORTA (Métodos de Pago) ---
$res_metodos = mysqli_query($conn, "SELECT metodo_pago, COUNT(*) as cant FROM cliente GROUP BY metodo_pago");
$lab_met = []; $cant_met = [];
while($m = mysqli_fetch_assoc($res_metodos)){
    $lab_met[] = $m['metodo_pago'];
    $cant_met[] = $m['cant'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Maestro | Burger Designers</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Helvetica', sans-serif; background: #f4f4f4; padding: 20px; }
        .main-wrapper { max-width: 1200px; margin: auto; }
        
        /* TARJETAS */
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .card-stat { background: #fff; padding: 20px; border: 3px solid #000; border-radius: 15px; box-shadow: 6px 6px 0px #dd1919; text-align: center; }
        .card-stat span { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #666; }
        .card-stat h3 { font-size: 28px; margin: 5px 0; color: #000; }

        /* GRÁFICOS */
        .grid-charts { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px; }
        .chart-container { background: #fff; padding: 20px; border: 3px solid #000; border-radius: 15px; box-shadow: 6px 6px 0px #000; }

        /* TABLAS (Tu estilo original) */
        .seccion-tabla { background: #fff; padding: 20px; border: 3px solid #000; border-radius: 15px; margin-top: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        th { background-color: #FFCC00; color: black; border: 1px solid #000; }
        
        .btn {
            padding: 8px 15px; background: #000; color: #fff; text-decoration: none;
            border-radius: 50px; font-size: 11px; font-weight: bold; display: inline-block;
        }
        .btn:hover { background: #dd1919; }

        .btn-grande { background: #dd1919; color: white; padding: 15px; display: block; text-align: center; 
                      border-radius: 10px; font-weight: bold; text-decoration: none; border: 3px solid #000; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="main-wrapper">
    <h1>ADMINISTRACIÓN BURGER DESIGNERS 🍔</h1>

    <a href="reportes_completos.php" class="btn-grande">VER HISTORIAL DE VENTAS DETALLADO 📈</a>

    <div class="grid-stats">
        <div class="card-stat">
            <span>Ventas de Hoy</span>
            <h3>$<?php echo number_format($total_hoy, 2); ?></h3>
        </div>
        <div class="card-stat">
            <span>Ventas del Mes</span>
            <h3>$<?php echo number_format($total_mes, 2); ?></h3>
        </div>
        <div class="card-stat" style="background: #fcc404;">
            <span>Top Producto</span>
            <h3>Special Burger</h3>
        </div>
    </div>

    <div class="grid-charts">
        <div class="chart-container">
            <h3>Rendimiento Semanal</h3>
            <canvas id="lineChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Métodos de Pago</h3>
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <div class="seccion-tabla">
        <h2>GESTIÓN DE USUARIOS</h2>
        <a href="register.html" class="btn" style="background: #28a745; margin-bottom: 10px;">+ Registrar Usuario</a>
        <form action="" method="post" style="margin-bottom: 15px;">
            <input type="text" name="search" placeholder="Buscar usuario..." style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <input type="submit" value="Buscar" class="btn">
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Fecha Reg.</th>
                <th>Acciones</th>
            </tr>
            <?php
            $sql_u = "SELECT * FROM registro";
            if(!empty($_POST["search"])) {
                $sql_u .= " WHERE name LIKE '%" . $_POST["search"] . "%'";
            }
            $res_u = $conn->query($sql_u);
            while($u = $res_u->fetch_assoc()) {
                echo "<tr>
                    <td>{$u['id']}</td>
                    <td>{$u['name']}</td>
                    <td>{$u['username']}</td>
                    <td>{$u['reg_date']}</td>
                    <td>
                        <a href='modificar.php?id={$u['id']}' class='btn'>Modificar</a>
                        <a href='#' onclick='eliminarUsuario({$u['id']})' class='btn' style='background:red;'>Eliminar</a>
                    </td>
                </tr>";
            }
            ?>
        </table>
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <button onclick="saliradmin()" style="padding: 10px 30px; background: #000; color: #fff; border-radius: 10px; cursor: pointer;">CERRAR SESIÓN</button>
    </div>
</div>

<script>
// GRÁFICO DE LÍNEAS
new Chart(document.getElementById('lineChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels_semana); ?>,
        datasets: [{
            label: 'Ventas USD',
            data: <?php echo json_encode($datos_semana); ?>,
            borderColor: '#dd1919',
            backgroundColor: 'rgba(221, 25, 25, 0.1)',
            fill: true,
            tension: 0.3
        }]
    }
});

// GRÁFICO DE TORTA
new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($lab_met); ?>,
        datasets: [{
            data: <?php echo json_encode($cant_met); ?>,
            backgroundColor: ['#fcc404', '#000', '#dd1919', '#555']
        }]
    }
});

function eliminarUsuario(id) {
    if (confirm("¿Eliminar usuario ID " + id + "?")) { window.location.href = "eliminar.php?id=" + id; }
}
function saliradmin() {
    if (confirm("¿Cerrar sesión?")) { window.location.href = "index.html"; }
}
</script>

</body>
</html>