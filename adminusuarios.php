<?php

require('fpdf/fpdf.php');
include('connection.php');

$query = "SELECT id, name, username, password, reg_date FROM registro";
$result = mysqli_query($conn, $query);

$pdf = new FPDF();

$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Informacion de Admin',0,1,'C');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(20,10,'ID',1,0,'C');
$pdf->Cell(40,10,'Nombre',1,0,'C');
$pdf->Cell(40,10,'Nombre de usuario',1,0,'C');
$pdf->Cell(40,10,utf8_decode('Contraseña'),1,0,'C');
$pdf->Cell(50,10,'Fecha de Registro',1,1,'C');

$pdf->SetFont('Arial','',12);
while($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(20,10,$row['id'],1,0,'C');
    $pdf->Cell(40,10,utf8_decode($row['name']),1,0,'L');
    $pdf->Cell(40,10,utf8_decode($row['username']),1,0,'L');
    $pdf->Cell(40,10,utf8_decode($row['password']),1,0,'L');
    $pdf->Cell(50,10,$row['reg_date'],1,1,'C');
}

$pdf->Output();
?>