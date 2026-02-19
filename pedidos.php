<?php
session_start();

// Initialize total price to 0
if (!isset($_SESSION['total_price'])) {
    $_SESSION['total_price'] = 0;
}

// Check which button was clicked and add its price to total
if (isset($_POST['hamburguesa_sencilla'])) {
    $_SESSION['total_price'] += 5;
}
if (isset($_POST['hamburguesa_pollo'])) {
    $_SESSION['total_price'] += 5;
}
if (isset($_POST['hamburguesa_doble'])) {
    $_SESSION['total_price'] += 7;
}
if (isset($_POST['perro_sencillo'])) {
    $_SESSION['total_price'] += 3;
}
if (isset($_POST['club_house'])) {
    $_SESSION['total_price'] += 10;
}
if (isset($_POST['coca_cola'])) {
    $_SESSION['total_price'] += 2;
}

// Save total price to database on form submit
if (isset($_POST['submit'])) {
    $conn = mysqli_connect("localhost", "root", "", "sistemadeventas");

    $total_price = $_SESSION['total_price'];
    $query = "INSERT INTO pago (total_price) VALUES ('$total_price')";
    mysqli_query($conn, $query);

    // Clear session variable
    $_SESSION['total_price'] = 0;

    mysqli_close($conn);
}
?>