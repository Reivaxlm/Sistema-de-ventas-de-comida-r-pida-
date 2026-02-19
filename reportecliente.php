<?php 
require('fpdf/fpdf.php'); 
include('connection.php'); 

$query = "SELECT nombre_cliente, apellido_cliente, cedula_cliente, telefono_cliente, metodo_pago FROM cliente";
$result = mysqli_query($conn, $query);

$pdf = new FPDF(); 
$pdf->AddPage(); 
$pdf->SetFont('Arial','B',14); 
$pdf->Cell(0,10,'INFORMACION DE CLIENTES',0,1,'C'); 
$pdf->Cell(0,10,date('d/m/Y'),0,1,'C'); 
$pdf->SetFont('Arial','B',10); 
$pdf->Cell(40,8,'Nombre',1,0,'C'); 
$pdf->Cell(40,8,'Apellido',1,0,'C'); 
$pdf->Cell(37,8,'Cedula',1,0,'C'); 
$pdf->Cell(37,8,'Telefono',1,0,'C'); 
$pdf->Cell(37,8,'Metodo de pago',1,1,'C'); 

$pdf->SetFont('Arial','',10); 
while($row = mysqli_fetch_assoc($result)) { 
    $pdf->Cell(40,8,$row['nombre_cliente'],1,0,'C'); 
    $pdf->Cell(40,8,$row['apellido_cliente'],1,0,'C'); 
    $pdf->Cell(37,8,$row['cedula_cliente'],1,0,'C'); 
    $pdf->Cell(37,8,$row['telefono_cliente'],1,0,'C'); 
    $pdf->Cell(37,8,$row['metodo_pago'],1,1,'C'); 
} 

$pdf->Output(); 
?>