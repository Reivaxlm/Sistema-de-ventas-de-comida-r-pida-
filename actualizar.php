<?php

include 'connection.php';

$id = $_POST['id'];
$name = $_POST['name'];
$username = $_POST['username'];
$password = $_POST['password'];

$sql = "UPDATE registro SET name='$name', username='$username', password='$password' WHERE id='$id'";
$result = mysqli_query($conn, $sql);


if ($result) {
    header("Location: admin.php");
    exit();
} else {
  echo "Error updating record: " . mysqli_error($conn);
}

mysqli_close($conn);


?>