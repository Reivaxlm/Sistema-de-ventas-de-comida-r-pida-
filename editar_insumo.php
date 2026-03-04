<?php
include("connection.php");

// 1. Obtener los datos actuales del insumo
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM inventario WHERE id = '$id'");
    $data = mysqli_fetch_assoc($query);
}

// 2. Procesar la actualización
if(isset($_POST['actualizar_insumo'])){
    $id_edit = $_POST['id'];
    $insumo = $_POST['insumo'];
    $cantidad = $_POST['cantidad'];
    $unidad = $_POST['unidad'];

    $update = mysqli_query($conn, "UPDATE inventario SET insumo='$insumo', cantidad='$cantidad', unidad='$unidad' WHERE id='$id_edit'");
    
    if($update){
        echo "<script>alert('¡Insumo actualizado!'); window.location='admin.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Insumo | Burger Designers</title>
    <link rel="stylesheet" href="css/admin.css"> </head>
<body style="background: #fcc404; display: flex; justify-content: center; align-items: center; height: 100vh;">

<div class="card" style="width: 400px; border: 5px solid #000; box-shadow: 15px 15px 0px #000;">
    <h2 style="text-align: center; text-transform: uppercase;">✏️ Editar Insumo</h2>
    
    <form action="" method="POST" style="display: flex; flex-direction: column; gap: 15px;">
        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

        <div class="input-group">
            <label style="font-weight: 900;">Nombre del Insumo:</label>
            <input type="text" name="insumo" value="<?php echo $data['insumo']; ?>" required style="width:100%; padding: 10px; border: 3px solid #000;">
        </div>

        <div class="input-group">
            <label style="font-weight: 900;">Cantidad en Stock:</label>
            <input type="number" name="cantidad" value="<?php echo $data['cantidad']; ?>" required style="width:100%; padding: 10px; border: 3px solid #000;">
        </div>

        <div class="input-group">
            <label style="font-weight: 900;">Unidad de Medida:</label>
            <select name="unidad" style="width:100%; padding: 10px; border: 3px solid #000; font-weight: bold;">
                <option value="Unidades" <?php if($data['unidad'] == 'Unidades') echo 'selected'; ?>>Unidades</option>
                <option value="Gramos" <?php if($data['unidad'] == 'Gramos') echo 'selected'; ?>>Gramos</option>
                <option value="Kilos" <?php if($data['unidad'] == 'Kilos') echo 'selected'; ?>>Kilos</option>
                <option value="Litros" <?php if($data['unidad'] == 'Litros') echo 'selected'; ?>>Litros</option>
            </select>
        </div>

        <button type="submit" name="actualizar_insumo" style="background: #000; color: #fff; padding: 15px; border: none; font-weight: 900; cursor: pointer; text-transform: uppercase;">
            Guardar Cambios
        </button>
        
        <a href="admin.php" style="text-align: center; color: #000; font-weight: bold; text-decoration: none;">← Cancelar y Volver</a>
    </form>
</div>

</body>
</html>