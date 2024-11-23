<?php
// Configuración de la conexión a la base de datos
$servidor = "localhost";
$nombreusuario = "root";
$password = "";
$bd = "restaurante";

// Conexión a la base de datos
$conectar = mysqli_connect($servidor, $nombreusuario, $password, $bd);

// Verificar conexión
if ($conectar->connect_error) {
    die("Conexión fallida: " . $conectar->connect_error);
}

// Variables para mensajes de alerta
$alerta = "";
$tipo_alerta = "";

// Insertar nueva orden
if (isset($_POST['agregar_orden'])) {
    $id_reserva = $_POST['id_reserva'];
    $id_menu = $_POST['id_menu'];
    $cantidad = $_POST['cantidad'];
    $instrucciones = $_POST['instrucciones'] ?? '';
    $estado = 'Pendiente';

    // Consulta para insertar la orden
    $sql = "INSERT INTO ordenes (id_reserva, id_menu, cantidad, estado, instrucciones) 
            VALUES ('$id_reserva', '$id_menu', '$cantidad', '$estado', '$instrucciones')";

    if ($conectar->query($sql) === TRUE) {
        $alerta = "Orden registrada con éxito.";
        $tipo_alerta = "success";
    } else {
        $alerta = "Error al registrar la orden: " . $conectar->error;
        $tipo_alerta = "danger";
    }
}

// Mostrar órdenes al hacer clic en "Ver Órdenes"
$ver_ordenes = isset($_POST['ver_ordenes']);

// Consulta para obtener todas las órdenes con detalles de la reserva y el plato
$consulta_ordenes = "SELECT ordenes.id, reservas.nombre_cliente, reservas.fecha, menu.nombre_plato, ordenes.cantidad, ordenes.estado, ordenes.instrucciones 
                     FROM ordenes 
                     JOIN reservas ON ordenes.id_reserva = reservas.id 
                     JOIN menu ON ordenes.id_menu = menu.id 
                     ORDER BY reservas.fecha ASC";
$resultado_ordenes = $conectar->query($consulta_ordenes);

// Exportar ordenes a Excel
if (isset($_POST['export_excel'])) {
    // Nombre del archivo
    $filename = "ordenes_" . date('Y-m-d') . ".csv";
    
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
        'Cliente',
        'Fecha',
        'Plato',
        'Cantidad',
        'Estado',
        'Instrucciones',
    ));
    
    // Escribir los datos
    while ($data = $resultado_ordenes->fetch_assoc()) {
        fputcsv($output, array(
            $data['id'],
            $data['nombre_cliente'],
            $data['fecha'],
            $data['nombre_plato'],
            $data['cantidad'],
            $data['estado'],
            $data['instrucciones'],
        ));
    }
    
    fclose($output);
    exit;
}

// Exportar órdenes a PDF
require('fpdf/fpdf.php');
class PDF extends FPDF {
    function MultiCellRow($datos) {
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
        
        $this->MultiCell(40, $height, $datos['nombre_plato'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 120, $y);
        
        $this->MultiCell(20, $height, $datos['cantidad'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 140, $y);
        
        $this->MultiCell(30, $height, $datos['estado'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 170, $y);
        
        $this->MultiCell(60, $height, $datos['instrucciones'], 1, 'L');
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
    $pdf->Cell(0, 10, 'Reporte de Ordenes', 0, 1, 'C');
    $pdf->Ln(5);
    // Encabezados de las columnas
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(10, 10, 'ID', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Cliente', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Fecha', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Plato', 1, 0, 'C');
    $pdf->Cell(20, 10, 'Cantidad', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Estado', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Instrucciones', 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 11);
    // Escribir los datos
    $consulta = "SELECT ordenes.id, reservas.nombre_cliente, reservas.fecha, menu.nombre_plato, ordenes.cantidad, ordenes.estado, ordenes.instrucciones 
                 FROM ordenes 
                 JOIN reservas ON ordenes.id_reserva = reservas.id 
                 JOIN menu ON ordenes.id_menu = menu.id 
                 ORDER BY reservas.fecha ASC";
    $resultado = $conectar->query($consulta);
    // Mostrar los datos
    while($row = $resultado->fetch_assoc()) {
        $datos = array(
            'id' => $row['id'],
            'nombre_cliente' => utf8_decode($row['nombre_cliente']),
            'fecha' => $row['fecha'],
            'nombre_plato' => utf8_decode($row['nombre_plato']),
            'cantidad' => $row['cantidad'],
            'estado' => $row['estado'],
            'instrucciones' => utf8_decode($row['instrucciones'])
        );
        $pdf->MultiCellRow($datos);
    }
    
    $pdf->Output('D', 'ordenes_'.date('Y-m-d').'.pdf');
    exit;
}
?>