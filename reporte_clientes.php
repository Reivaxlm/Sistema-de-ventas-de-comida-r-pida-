<?php
include 'connection.php';
// Agrupa por cédula para ver cuántas veces ha comprado la misma persona
$res = mysqli_query($conn, "SELECT nombre_cliente, apellido_cliente, telefono_cliente, COUNT(*) as visitas, SUM(monto) as total_gastado 
                            FROM cliente GROUP BY cedula_cliente ORDER BY visitas DESC");
?>
<div class="seccion-datos" style="max-width: 800px; margin: auto;">
    <h2>Ranking de Clientes 🏆</h2>
    <table class="tabla-reporte" style="width: 100%; border-collapse: collapse;">
        <tr style="background: #1a1a1a; color: white;">
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Compras</th>
            <th>Total</th>
        </tr>
        <?php while($c = mysqli_fetch_assoc($res)): ?>
        <tr style="text-align: center;">
            <td style="padding: 10px;"><?php echo $c['nombre_cliente']; ?></td>
            <td><?php echo $c['telefono_cliente']; ?></td>
            <td><?php echo $c['visitas']; ?> veces</td>
            <td style="font-weight: 900;">$<?php echo number_format($c['total_gastado'], 2); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>