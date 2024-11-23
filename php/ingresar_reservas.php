<?php
// Conexión a la base de datos
$servidor = "localhost";
$nombreusuario = "root";
$password = "";
$bd = "restaurante";

$conn = mysqli_connect($servidor, $nombreusuario, $password, $bd);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables de alerta
$alerta = "";
$tipo_alerta = "";

// Insertar nueva reserva
if (isset($_POST['agregar_reserva'])) {
    $nombre_cliente = $_POST['nombre_cliente'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $personas = $_POST['personas'];
    $telefono = $_POST['telefono'];
    $estado = $_POST['estado'];

    // Consulta para insertar la reserva
    $sql = "INSERT INTO reservas (nombre_cliente, fecha, hora, numero_personas, telefono, estado) 
            VALUES ('$nombre_cliente', '$fecha', '$hora', '$personas', '$telefono', '$estado')";

    if ($conn->query($sql) === TRUE) {
        $alerta = "Reserva agregada con éxito.";
        $tipo_alerta = "success";
    } else {
        $alerta = "Error al agregar reserva: " . $conn->error;
        $tipo_alerta = "danger";
    }
}

// Ver reservas al hacer clic en el botón
$ver_reservas = isset($_POST['ver_reservas']);

// Consulta para mostrar todas las reservas
$consulta_reservas = "SELECT * FROM reservas ORDER BY fecha ASC";
$resultado_reservas = $conn->query($consulta_reservas);

// Exportar reservas a Excel
if (isset($_POST['export_excel'])) {
    // Nombre del archivo
    $filename = "reservas_" . date('Y-m-d') . ".csv";
    
    // Headers para forzar la descarga
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Crear el archivo
    $output = fopen('php://output', 'w');
    
    // Agregar BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Encabezados de las columnas
    fputcsv($output, array(
        'ID',
        'Nombre del Cliente',
        'Fecha',
        'Hora',
        'Número de Personas',
        'Teléfono',
        'Estado'
    ));
    
    // Escribir los datos
    while ($data = $resultado_reservas->fetch_assoc()) {
        fputcsv($output, array(
            $data['id'],
            $data['nombre_cliente'],
            $data['fecha'],
            $data['hora'],
            $data['numero_personas'],
            $data['telefono'],
            $data['estado']
        ));
    }
    
    fclose($output);
    exit;
}

// Exportar reservas a PDF
require('fpdf/fpdf.php');
class PDF extends FPDF {
    function MultiCellRow($datos) {
        // Altura de la fila
        $height = 6;
        $x = $this->GetX();
        $y = $this->GetY();
        $max_y = $y;
        
        $this->MultiCell(10, $height, $datos['id'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 10, $y);
        
        $this->MultiCell(40, $height, $datos['nombre_cliente'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 50, $y);
        
        $this->MultiCell(30, $height, $datos['fecha'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 80, $y);
        
        $this->MultiCell(20, $height, $datos['hora'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 100, $y);
        
        $this->MultiCell(30, $height, $datos['numero_personas'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 130, $y);
        
        $this->MultiCell(30, $height, $datos['telefono'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 160, $y);
        
        $this->MultiCell(30, $height, $datos['estado'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        
        $this->SetXY($x, $max_y);
    }
}

if (isset($_POST['export_pdf'])) {
    // Crear el archivo PDF
    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Reporte de Reservas', 0, 1, 'C');
    $pdf->Ln(5);
    // Encabezados de las columnas
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(10, 10, 'ID', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Nombre del Cliente', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Fecha', 1, 0, 'C');
    $pdf->Cell(20, 10, 'Hora', 1, 0, 'C');
    $pdf->Cell(30, 10, '# Personas', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Telefono', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Estado', 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 11);
    
    $consulta = "SELECT * FROM reservas ORDER BY fecha ASC";
    $resultado = $conn->query($consulta);
    // Escribir los datos
    while($row = $resultado->fetch_assoc()) {
        $datos = array(
            'id' => $row['id'],
            'nombre_cliente' => utf8_decode($row['nombre_cliente']),
            'fecha' => $row['fecha'],
            'hora' => $row['hora'],
            'numero_personas' => $row['numero_personas'],
            'telefono' => $row['telefono'],
            'estado' => $row['estado']
        );
        $pdf->MultiCellRow($datos);
    }
    
    $pdf->Output('D', 'reservas_'.date('Y-m-d').'.pdf');
    exit;
}
?>