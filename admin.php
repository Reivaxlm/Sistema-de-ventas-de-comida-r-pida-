<!DOCTYPE html>
<html>
<head>
	<title>Menu de admin</title>
	<link rel="stylesheet" type="text/css" href="css/admin.css">
	
	<style>

		table {
			border-collapse: collapse;
			width: 100%;
		}

		th, td {
			text-align: left;
			padding: 8px;
		}

		tr:nth-child(even){background-color: #f2f2f2}

		th {
			background-color: #FFCC00

;
			color: black;
		}

		.btn {
			padding: 1px; /*espacio alrededor texto*/
background-color: #000; /*color botón*/
color: #ffffff; /*color texto*/
text-decoration: none; /*decoración texto*/
text-transform: uppercase; /*capitalización texto*/
font-family: 'Helvetica', sans-serif; /*tipografía texto*/
border-radius: 50px; /*bordes redondos*/
width: 200px;

margin: 0 auto;
align-items: center;
justify-content: center;
display: flex;


		}

		.btn:hover {
			background-color: #3e8e41;
		}

	</style>
	
</head>
<body>
	<h1>MENÚ ADMIN</h1>
	<a href="register.html">Registrar nuevo usuario</a>
	<form action="" method="post">
		<label for="search">Buscar Nombre:</label>
		<input type="text" id="search" name="search">
		<input type="submit" value="Buscar">
	</form>

	<table>
		<tr>
			<th>ID <a href="?orderby=id_asc">&#9650;</a><a href="?orderby=id_desc">&#9660;</a></th>
			<th>Nombre<a href="?orderby=name_asc">&#9650;</a><a href="?orderby=id_desc">&#9660;</a></th>
			<th>Usuario<a href="?orderby=username_asc">&#9650;</a><a href="?orderby=id_desc">&#9660;</a></th>
			<th>Contraseña</th>
			<th>Fecha de Registro<a href="?orderby=reg_date_asc">&#9650;</a><a href="?orderby=id_desc">&#9660;</a></th>
			<th>Acciones</th>
		</tr>

		<?php
			include("connection.php");

			$sql = "SELECT * FROM registro";

			if(!empty($_POST["search"])) {
				$sql .= " WHERE name LIKE '%" . $_POST["search"] . "%'";
			}

			if(isset($_GET['orderby'])) {
				if($_GET['orderby'] == 'id_asc') {
					$sql .= " ORDER BY id ASC";
				}
				elseif($_GET['orderby'] == 'id_desc') {
					$sql .= " ORDER BY id DESC";
				}
				elseif($_GET['orderby'] == 'name_asc') {
					$sql .= " ORDER BY name ASC";
				}
				elseif($_GET['orderby'] == 'name_desc') {
					$sql .= " ORDER BY name DESC";
				}
				elseif($_GET['orderby'] == 'username_asc') {
					$sql .= " ORDER BY username ASC";
				}
				elseif($_GET['orderby'] == 'username_desc') {
					$sql .= " ORDER BY username DESC";
				}
				elseif($_GET['orderby'] == 'reg_date_asc') {
					$sql .= " ORDER BY username ASC";
				}
				elseif($_GET['orderby'] == 'reg_date_desc') {
					$sql .= " ORDER BY username DESC";
				}
			}

			$result = $conn->query($sql);

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					echo "<tr>";
					echo "<td>" . $row["id"] . "</td>";
					echo "<td>" . $row["name"] . "</td>";
					echo "<td>" . $row["username"] . "</td>";
					echo "<td>" . $row["password"] . "</td>";
					echo "<td>" . $row["reg_date"] . "</td>";
					echo "<td>";
					echo "<a href='modificar.php?id=" . $row["id"] . "' class='btn'>Modificar</a> ";					
					echo "<a href='#' onclick='eliminarUsuario(" . $row["id"] . ")' class='btn'>Eliminar</a>";
					echo "</td>";
					echo "</tr>";
				}
			} else {
				echo "No se encontraron resultados.";
			}

			$conn->close();
		?>
	</table>
	<a href="adminusuarios.php"><button>Descargar</button></a>
	<a href="#" onclick="saliradmin()"><button>Salir</button></a>

	<script>
  
  function saliradmin() {
    
	var respuesta = confirm("¿Estás seguro de que quieres salir?");
    
	if (respuesta) {
      window.location.href = "index.html"; // Aquí puedes poner el enlace que quieras
    }
  }
  function eliminarUsuario(id) {
    var respuesta = confirm("¿Estás seguro de que quieres eliminar al usuario con id " + id + "?");
    if (respuesta) {
      window.location.href = "eliminar.php?id=" + id;
    }
  }
</script>

</body>
</html>