<?php
include("connection.php");

// 1. Lógica de Búsqueda y Filtro
$where = "WHERE 1=1";
if (!empty($_POST['buscar_cliente'])) {
    $busqueda = mysqli_real_escape_string($conn, $_POST['buscar_cliente']);
    $where .= " AND (nombre_cliente LIKE '%$busqueda%' OR cedula_cliente LIKE '%$busqueda%' OR referencia LIKE '%$busqueda%')";
}

// 2. Consulta principal de todos los pedidos
$sql = "SELECT * FROM cliente $where ORDER BY id DESC";
$resultado = mysqli_query($conn, $sql);

// 3. Cálculos para el resumen superior
$res_stats = mysqli_query($conn, "SELECT SUM(monto) as total, COUNT(*) as cantidad FROM cliente $where");
$stats = mysqli_fetch_assoc($res_stats);
$monto_total = $stats['total'] ?? 0;
$cantidad_pedidos = $stats['cantidad'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Detallado | Burger Designers</title>
    <link rel="stylesheet" href="css/cliente.css">
    <style>
        body { background-color: #f4f4f4; padding: 20px; font-family: 'Helvetica', sans-serif; }
        .container-reporte { max-width: 1100px; margin: 0 auto; background: white; padding: 30px; border: 3px solid #000; border-radius: 20px; box-shadow: 10px 10px 0px #000; }
        
        /* BARRA DE ESTADÍSTICAS */
        .resumen-negro { display: flex; justify-content: space-between; margin-bottom: 20px; padding: 20px; background: #000; color: #fff; border-radius: 12px; border: 2px solid #fcc404; }
        .resumen-negro span { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        .resumen-negro strong { font-size: 22px; color: #fcc404; display: block; }

        /* BUSCADOR */
        .filtros-barra { display: flex; gap: 15px; background: #fcc404; padding: 20px; border-radius: 12px; border: 3px solid #000; margin-bottom: 25px; align-items: flex-end; }
        .input-filtro { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .input-filtro label { font-weight: bold; font-size: 13px; }
        .input-filtro input { padding: 12px; border: 2px solid #000; border-radius: 8px; font-size: 15px; }

        /* TABLA */
        .tabla-completa { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
        .tabla-completa th { background: #1a1a1a; color: #fcc404; padding: 15px; text-align: left; border: 1px solid #333; text-transform: uppercase; font-size: 12px; }
        .tabla-completa td { padding: 15px; border-bottom: 1px solid #ddd; font-size: 14px; border-right: 1px solid #eee; }
        .tabla-completa tr:hover { background: #fff8e1; }

        .monto-v { font-weight: 900; color: #dd1919; font-size: 16px; }
        .metodo-tag { padding: 5px 12px; border-radius: 50px; font-size: 11px; font-weight: 800; border: 2px solid #000; background: #eee; }
        
        .btn-pdf { background: #28a745; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: bold; font-size: 12px; border: 2px solid #000; }
        .btn-pdf:hover { background: #218838; }
    </style>
</head>
<body>

    <div class="container-reporte">
        <div class="div-titulo" style="position: static; transform: none; margin-bottom: 30px; text-align: center;">
            <h1 style="display: block; margin: 0 auto;">Historial de Ventas 📊</h1>
        </div>

        <div class="resumen-negro">
            <div>
                <span>Total Pedidos</span>
                <strong><?php echo $cantidad_pedidos; ?></strong>
            </div>
            <div style="text-align: right;">
                <span>Venta Total Filtrada</span>
                <strong>$<?php echo number_format($monto_total, 2); ?></strong>
            </div>
        </div>

        <form method="POST" class="filtros-barra">
            <div class="input-filtro">
                <label>Buscar por nombre, cédula o número de referencia:</label>
                <input type="text" name="buscar_cliente" placeholder="Ej: Luis Meléndez o 1234..." value="<?php echo $_POST['buscar_cliente'] ?? ''; ?>">
            </div>
            <button type="submit" class="btn-confirmar" style="margin: 0; height: 48px; width: 150px;">BUSCAR</button>
            <a href="reportes_completos.php" class="btn-regresar-premium" style="margin: 0; height: 48px; align-items: center; display: flex;">LIMPIAR</a>
        </form>

        <table class="tabla-completa">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Cédula</th>
                    <th>Pago</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($resultado) > 0) {
                    while($row = mysqli_fetch_assoc($resultado)): 
                ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo $row['nombre_cliente'] . " " . $row['apellido_cliente']; ?></strong></td>
                    <td><?php echo $row['cedula_cliente']; ?></td>
                    <td><span class="metodo-tag"><?php echo $row['metodo_pago']; ?></span></td>
                    <td><code><?php echo $row['referencia']; ?></code></td>
                    <td class="monto-v">$<?php echo number_format($row['monto'], 2); ?></td>
                    <td>
                        <a href="factura.php" class="btn-pdf">PDF</a>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                } else {
                    // MENSAJE DE "NO HAY RESULTADOS"
                    echo "<tr>
                            <td colspan='7' style='text-align:center; padding: 60px;'>
                                <div style='font-size: 50px;'>🔍</div>
                                <h3 style='color: #666;'>No se encontraron ventas</h3>
                                <p style='color: #999;'>Prueba con otro nombre o limpia el filtro.</p>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <div style="margin-top: 40px; display: flex; justify-content: center; gap: 20px;">
            <a href="admin.php" class="btn-regresar-premium">← VOLVER AL PANEL</a>
            <button onclick="window.print()" class="btn-regresar-premium" style="background: #000; color: white !important;">🖨️ IMPRIMIR LISTADO</button>
        </div>
    </div>

</body>
</html>