<?php
include("connection.php");

$filename = "REPORT_BURGER_DESIGNERS_" . date('Y-m-d') . ".xls";

// Cabeceras específicas para que Excel reconozca el contenido como una hoja de cálculo con estilos
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);

$resultado = mysqli_query($conn, "SELECT * FROM cliente ORDER BY reg_date DESC");

// Definición de colores para reuso
$n = "#000000"; // Negro
$a = "#FCC404"; // Amarillo Burger
$r = "#DD1919"; // Rojo
$b = "#FFFFFF"; // Blanco

echo "
<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta http-equiv=\"Content-type\" content=\"text/html;charset=utf-8\" />
</head>
<body>
<table border='1' style='border-collapse:collapse; font-family:Arial;'>
    <tr>
        <th colspan='7' bgcolor='$n' style='color:$a; font-size:16pt; height:40px; border:2px solid $n;'>
            BURGER DESIGNERS - REPORTE DE VENTAS
        </th>
    </tr>
    <tr><td colspan='7' style='text-align:right; font-size:9pt;'>Emisión: " . date('d/m/Y H:i') . "</td></tr>

    <tr bgcolor='$a' style='font-weight:bold; height:30px;'>
        <th style='border:1px solid $n;'>ID</th>
        <th style='border:1px solid $n;'>FECHA</th>
        <th style='border:1px solid $n;'>CLIENTE</th>
        <th style='border:1px solid $n;'>CEDULA</th>
        <th style='border:1px solid $n;'>METODO</th>
        <th style='border:1px solid $n;'>REFERENCIA</th>
        <th style='border:1px solid $n;'>MONTO USD</th>
    </tr>";

    $total = 0;
    while ($row = mysqli_fetch_assoc($resultado)) {
        $total += $row['monto'];
        echo "<tr>
                <td align='center' style='border:1px solid #ccc;'>#" . $row['id'] . "</td>
                <td align='center' style='border:1px solid #ccc;'>" . date('d/m/y', strtotime($row['reg_date'])) . "</td>
                <td style='border:1px solid #ccc;'>" . strtoupper($row['nombre_cliente'] . " " . $row['apellido_cliente']) . "</td>
                <td align='center' style='border:1px solid #ccc;'>" . $row['cedula_cliente'] . "</td>
                <td align='center' style='border:1px solid #ccc;'>" . strtoupper($row['metodo_pago']) . "</td>
                <td align='center' style='border:1px solid #ccc;'>`" . $row['referencia'] . "</td>
                <td align='right' style='border:1px solid #ccc; color:$r; font-weight:bold;'>$ " . number_format($row['monto'], 2) . "</td>
              </tr>";
    }

    // ESPACIO Y TOTALES
    echo "<tr><td colspan='7'></td></tr>
    <tr>
        <td colspan='5' align='right' bgcolor='#eeeeee' style='border:2px solid $n; font-weight:bold;'>TOTAL RECAUDADO:</td>
        <td colspan='2' align='right' bgcolor='$a' style='border:2px solid $n; font-weight:bold; font-size:12pt;'>
            $ " . number_format($total, 2) . "
        </td>
    </tr>
</table>
</body>
</html>";
?>