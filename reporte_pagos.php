<?php
include 'connection.php';
$res = mysqli_query($conn, "SELECT metodo_pago, COUNT(*) as cantidad, SUM(monto) as total_m 
                            FROM cliente GROUP BY metodo_pago");
?>
<div class="seccion-datos" style="max-width: 600px; margin: auto;">
    <h2>Análisis de Pagos</h2>
    <table class="tabla-reporte" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <tr style="background: #000; color: #fcc404;">
            <th style="padding: 10px;">Método</th>
            <th>Uso</th>
            <th>Total Recaudado</th>
        </tr>
        <?php while($r = mysqli_fetch_assoc($res)): ?>
        <tr style="text-align: center; border-bottom: 1px solid #eee;">
            <td style="padding: 10px;"><strong><?php echo $r['metodo_pago']; ?></strong></td>
            <td><?php echo $r['cantidad']; ?> pedidos</td>
            <td style="color: #dd1919; font-weight: bold;">$<?php echo number_format($r['total_m'], 2); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>