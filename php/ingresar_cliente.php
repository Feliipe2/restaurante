<?php
// Configuración de conexión a la base de datos
$servidor = "localhost";
$nombreusuario = "root";
$password = "";
$bd = "restaurante";

// Conexión a la base de datos
$conectar = mysqli_connect($servidor, $nombreusuario, $password, $bd);

// Verificamos si la conexión tiene errores
if ($conectar->connect_error) {
    die("Conexión fallida: " . $conectar->connect_error);
}

// Variables para mensajes de alerta
$alerta = "";
$tipo_alerta = "";

// Insertar nuevo cliente
if (isset($_POST['agregar_cliente'])) {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'] ?? '';

    // Consulta SQL para insertar cliente
    $sql = "INSERT INTO clientes (nombre, email, telefono, direccion) VALUES ('$nombre', '$email', '$telefono', '$direccion')";

    if ($conectar->query($sql) === TRUE) {
        $alerta = "Cliente registrado con éxito.";
        $tipo_alerta = "success";
    } else {
        $alerta = "Error al registrar el cliente: " . $conectar->error;
        $tipo_alerta = "danger";
    }
}

// Mostrar clientes al hacer clic en "Ver Clientes"
$ver_clientes = isset($_POST['ver_clientes']);

// Consulta para obtener todos los clientes
$consulta_clientes = "SELECT * FROM clientes ORDER BY nombre ASC";
$resultado_clientes = $conectar->query($consulta_clientes);

// Exportar clientes a Excel
if (isset($_POST['export_excel'])) {
    // Nombre del archivo
    $filename = "clientes_" . date('Y-m-d') . ".csv";
    
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
        'Nombre',
        'Email',
        'Teléfono',
        'Dirección'
    ));
    
    // Escribir los datos
    while ($data = $resultado_clientes->fetch_assoc()) {
        fputcsv($output, array(
            $data['id'],
            $data['nombre'],
            $data['email'],
            $data['telefono'],
            $data['direccion']
        ));
    }
    
    fclose($output);
    exit;
}

// Exportar clientes a PDF
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
        
        $this->MultiCell(40, $height, $datos['nombre'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 50, $y);
        
        $this->MultiCell(50, $height, $datos['email'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 100, $y);
        
        $this->MultiCell(30, $height, $datos['telefono'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 130, $y);
        
        $this->MultiCell(80, $height, $datos['direccion'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        
        $this->SetXY($x, $max_y);
    }
}

if (isset($_POST['export_pdf'])) {
    // Crear el objeto PDF
    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Reporte de Clientes', 0, 1, 'C');
    $pdf->Ln(5);
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(10, 10, 'ID', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Nombre', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Email', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Telefono', 1, 0, 'C');
    $pdf->Cell(80, 10, 'Direccion', 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 11);
    // Consulta SQL para obtener los clientes
    $consulta = "SELECT * FROM clientes ORDER BY nombre ASC";
    $resultado = $conectar->query($consulta);
    // Mostrar los datos
    while($row = $resultado->fetch_assoc()) {
        $datos = array(
            'id' => $row['id'],
            'nombre' => utf8_decode($row['nombre']),
            'email' => utf8_decode($row['email']),
            'telefono' => $row['telefono'],
            'direccion' => utf8_decode($row['direccion'])
        );
        $pdf->MultiCellRow($datos);
    }
    
    $pdf->Output('D', 'clientes_'.date('Y-m-d').'.pdf');
    exit;
}
?>