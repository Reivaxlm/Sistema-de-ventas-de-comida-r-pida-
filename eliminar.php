<?php
    include("connection.php");

    $id = $_GET["id"];

    $sql = "DELETE FROM registro WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Registro eliminado exitosamente";
        
    } else {
        echo "Error al eliminar registro: " . $conn->error;
    }

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <a href="admin.php"><button>Volver</button></a>
</head>
<body>
    
</body>
</html>