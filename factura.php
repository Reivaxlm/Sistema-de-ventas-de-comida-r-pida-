<?php
require('fpdf/fpdf.php');
include 'connection.php';

class PDF extends FPDF
{
    function Header()
    {
       
        $this->SetFont('Arial','B',15);
        $this->Cell(80);
        $this->Cell(30,10,'Burguers Designers',0,0,'C');
        $this->Ln(20);
    }

    function Footer()
    {
        
        $this->SetY(-15);

        $this->SetFont('Arial','I',8);

        $this->Cell(0,10,'Gracias por su compra',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);

$sql = "SELECT nombre_cliente, apellido_cliente, cedula_cliente, telefono_cliente, metodo_pago FROM cliente ORDER BY id DESC LIMIT 1";
$resultado = mysqli_query($conn, $sql);
$cliente = mysqli_fetch_assoc($resultado);

$pdf->Cell(30,10,'Nombre: '. $cliente['nombre_cliente']);
$pdf->Ln();
$pdf->Cell(30,10,'Apellido: '. $cliente['apellido_cliente']);
$pdf->Ln();
$pdf->Cell(30,10,'Cedula: '. $cliente['cedula_cliente']);
$pdf->Ln();
$pdf->Cell(30,10,'Telefono: '. $cliente['telefono_cliente']);
$pdf->Ln();
$pdf->Cell(30,10,'Banco: '. $cliente['metodo_pago']);
$pdf->Ln();
$pdf->Ln();

$subtotal = 1000; 
$iva = $subtotal * 0.12;
$total = $subtotal + $iva; 
$pdf->Cell(30,10,'Subtotal: $'.$subtotal);
$pdf->Ln();
$pdf->Cell(30,10,'IVA (12%): $'.$iva);
$pdf->Ln();
$pdf->Cell(30,10,'Total: $'.$total);
$pdf->Ln();
$pdf->Ln();

$pdf->Cell(0,10,'-------------------------------------------',0,0,'C');
$pdf->Ln();
$pdf->Cell(0,10,'Gracias por su compra',0,0,'C');
$pdf->Ln();
$pdf->Cell(0,10,'-------------------------------------------',0,0,'C');

$pdf->Output('factura.pdf','F');

header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="factura.pdf"');
readfile('factura.pdf');

?>