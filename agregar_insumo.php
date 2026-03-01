<?php
include("connection.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Insumo | Burger Designers</title>
    <link rel="stylesheet" href="css/admin.CSS">
    <style>
        .container-insumo {
            max-width: 500px;
            margin: 50px auto;
        }
        .form-insumo {
            background: #fff;
            padding: 30px;
            border: 4px solid #000;
            border-radius: 15px;
            box-shadow: 10px 10px 0px #000;
        }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-weight: 900; margin-bottom: 8px; text-transform: uppercase; }
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px;
            border: 3px solid #000;
            border-radius: 8px;
            font-family: inherit;
            font-weight: bold;
            box-sizing: border-box;
        }
        .btn-save {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: #fff;
            border: 3px solid #000;
            border-radius: 10px;
            font-weight: 900;
            cursor: pointer;
            box-shadow: 4px 4px 0px #000;
            text-transform: uppercase;
        }
        .btn-save:active { transform: translate(2px, 2px); box-shadow: 2px 2px 0px #000; }
        .btn-back { display: block; text-align: center; margin-top: 20px; color: #000; font-weight: bold; text-decoration: none; }
    </style>
</head>
<body>

<div class="container-insumo">
    <div class="form-insumo">
        <h2 style="margin-top: 0; text-align: center;">📥 NUEVO INSUMO</h2>
        
        <form action="guardar_produccion.php" method="POST">
            <div class="input-group">
                <label>Nombre del Insumo:</label>
                <input type="text" name="nuevo_insumo" placeholder="Ej: Carne de Res" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="input-group">
                    <label>Cantidad:</label>
                    <input type="number" name="nueva_cantidad" placeholder="0" required>
                </div>
                <div class="input-group">
                    <label>Unidad:</label>
                    <select name="nueva_unidad" required>
                        <option value="Unidades">Unidades</option>
                        <option value="Gramos">Gramos</option>
                        <option value="Kilos">Kilos</option>
                        <option value="Litros">Litros</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="btn_guardar_insumo" class="btn-save">
                Guardar en Inventario
            </button>
        </form>
        
        <a href="admin.php" class="btn-back">← Volver al Panel</a>
    </div>
</div>

</body>
</html>