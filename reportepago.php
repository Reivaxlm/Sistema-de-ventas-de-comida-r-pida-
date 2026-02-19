<?php

require('fpdf/fpdf.php');
include('connection.php');

$query = "SELECT pago, pag_dato FROM pago";
$result = mysqli_query($conn, $query);

$pdf = new FPDF();

$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'INFORMACION DE PAGOS',0,1,'C');
$pdf->Cell(0,10,date('d/m/Y'),0,1,'C');


$pdf->SetFont('Arial','B',12);
$pdf->Cell(95,10,'Pago',1,0,'C');
$pdf->Cell(95,10,'Fecha de Pago',1,1,'C');

$pdf->SetFont('Arial','',12);
while($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(95,10,$row['pago'],1,0,'C');
    $pdf->Cell(95,10,$row['pag_dato'],1,1,'C');
}

$pdf->Output();
?>