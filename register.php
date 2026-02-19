<?php
include('connection.php');

if(isset($_POST['name']) && isset($_POST['username']) && isset($_POST['password1']) && isset($_POST['password2'])){
	$name = $_POST['name'];
	$username = $_POST['username'];
	$password1 = $_POST['password1'];
	$password2 = $_POST['password2'];

	if($password1 == $password2){
		$query = "INSERT INTO registro (name, username, password) VALUES ('$name', '$username', '$password1')";
		$result = mysqli_query($conn, $query);

		if($result){
			header("Location: admin.php");
            exit();
		}else{
			echo "Error al registrar el usuario";
		}
	}else{
		echo "Las contraseñas no coinciden";
	}
}
?>