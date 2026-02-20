<?php
// Desactivar reporte de errores visuales para que no rompan el PDF
error_reporting(0);
ini_set('display_errors', 0);

require('fpdf/fpdf.php');
include 'connection.php';

// 1. Obtener el último pedido
$sql = "SELECT * FROM cliente ORDER BY id DESC LIMIT 1";
$resultado = mysqli_query($conn, $sql);
$cliente = mysqli_fetch_assoc($resultado);

if (!$cliente) { die("No se encontró el pedido."); }

// Función mejorada para evitar el error de "illegal character"
function codificar($texto) {
    // Eliminamos caracteres que FPDF no entiende (como emojis) y convertimos
    $texto = str_replace('🍔', '', $texto); 
    return iconv('UTF-8', 'windows-1252//IGNORE', $texto);
}

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFillColor(252, 196, 4);
        $this->Rect(0, 0, 210, 40, 'F');
        
        $this->SetFont('Arial', 'B', 22);
        $this->SetTextColor(0, 0, 0);
        // Quitamos el emoji aquí también por seguridad
        $this->Cell(0, 10, codificar('BURGER DESIGNERS'), 0, 1, 'C');
        
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, codificar('El arte de la hamburguesa artesanal'), 0, 1, 'C');
        $this->Ln(15);
    }

    function Footer()
    {
        $this->SetY(-25);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        $this->Cell(0, 10, codificar('Esta factura es un comprobante digital de su compra.'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(221, 25, 25); 
        $this->Cell(0, 10, codificar('¡GRACIAS POR TU COMPRA!'), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

// --- SECCIÓN: DATOS DEL CLIENTE ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(0, 10, codificar(' INFORMACIÓN DEL CLIENTE'), 0, 1, 'L', true);
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(95, 8, codificar('Cliente: ' . $cliente['nombre_cliente'] . ' ' . $cliente['apellido_cliente']), 0, 0);
$pdf->Cell(95, 8, codificar('Cédula: ' . $cliente['cedula_cliente']), 0, 1);
$pdf->Cell(95, 8, codificar('Teléfono: ' . $cliente['telefono_cliente']), 0, 0);
$pdf->Cell(95, 8, codificar('Fecha: ' . date('d/m/Y')), 0, 1);
$pdf->Ln(10);

// --- SECCIÓN: DETALLES DEL PAGO ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, codificar(' DETALLES DE LA TRANSACCIÓN'), 0, 1, 'L', true);
$pdf->Ln(2);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(95, 8, codificar('Método de Pago: ' . $cliente['metodo_pago']), 0, 0);
$referencia = (!empty($cliente['referencia'])) ? $cliente['referencia'] : 'N/A';
$pdf->Cell(95, 8, codificar('Referencia: #' . $referencia), 0, 1);
$pdf->Ln(10);

// --- SECCIÓN: TOTALES ---
$pdf->SetDrawColor(221, 25, 25); 
$pdf->SetLineWidth(0.5);
$pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
$pdf->Ln(5);

$montoTotal = (float)$cliente['monto']; 
$subtotal = $montoTotal / 1.16; 
$iva = $montoTotal - $subtotal;

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130, 10, '', 0, 0);
$pdf->Cell(30, 10, 'Subtotal:', 0, 0);
$pdf->Cell(30, 10, '$' . number_format($subtotal, 2), 0, 1, 'R');

$pdf->Cell(130, 10, '', 0, 0);
$pdf->Cell(30, 10, 'IVA (16%):', 0, 0);
$pdf->Cell(30, 10, '$' . number_format($iva, 2), 0, 1, 'R');

$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(221, 25, 25);
$pdf->Cell(130, 12, '', 0, 0);
$pdf->Cell(30, 12, 'TOTAL:', 0, 0);
$pdf->Cell(30, 12, '$' . number_format($montoTotal, 2), 0, 1, 'R');

// --- FINALIZACIÓN ---
ob_clean(); // Limpia cualquier carácter basura
$pdf->Output('I', 'Factura_Burger_Designers.pdf');
?>