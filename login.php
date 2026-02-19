<?php
session_start();
include('connection.php');

if(isset($_POST['username']) && isset($_POST['password'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM registro WHERE username=? AND password=?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($result !== false && $row = mysqli_fetch_assoc($result)){
        if ($username == "admin" && $password == "admin") {
            $_SESSION['username'] = $username;
            header('Location: admin.php');
        } else {
            if(isset($row['entrar'])){
                if($row['entrar'] == 0){
                    $_SESSION['username'] = $username;
                    $_SESSION['id'] = $row['id'];
                    header('Location: vendedor.html');
                    $entrar = $row['entrar'] + 1;
                   
                    $stmt = mysqli_prepare($conn, "UPDATE registro SET entrar = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, "ii", $entrar, $row['id']);
                    mysqli_stmt_execute($stmt);
                } elseif($row['entrar'] > 0) {
                    $_SESSION['username'] = $username;
                    $_SESSION['id'] = $row['id'];
                    header('Location: Principal.php');
                }
            } 
            
        }
    } else {
        echo "Nombre de Usuario o Contraseña incorrectos";
    }
}
?>