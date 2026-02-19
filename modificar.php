<?php
    include("connection.php");

    $id = $_GET["id"];

    $sql = "SELECT * FROM registro WHERE id='$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modificar Registro</title>
    <script>
  
  function confirmar() {
    
    var respuesta = confirm("¿Estás seguro de que quieres actualizar este registro?");
    
    if (respuesta == true) {
      return true;
    }
    else {
      return false;
    }
  }
 </script>
</head>
<body>
    <h1>Modificar Registro</h1>

    <form action="actualizar.php" method="post">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>"><br><br>
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" value="<?php echo $row['username']; ?>"><br><br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" value="<?php echo $row['password']; ?>"><br><br>
        <input type="submit" value="Actualizar" onclick="return confirmar();">
        <a href="admin.php.html"><button>Volver</button></a>;
    </form>
    
</body>
</html>