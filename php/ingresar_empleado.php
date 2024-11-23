<?php
// Configuración de conexión a la base de datos
$servidor = "localhost";
$nombreusuario = "root";
$password = "";
$bd = "restaurante";

// Conexión a la base de datos
$conectar = mysqli_connect($servidor, $nombreusuario, $password, $bd);

// Verificar la conexión
if ($conectar->connect_error) {
    die("Conexión fallida: " . $conectar->connect_error);
}

// Variables para mensajes de alerta
$alerta = "";
$tipo_alerta = "";

// Insertar nuevo empleado
if (isset($_POST['agregar_empleado'])) {
    $nombre = $_POST['nombre'];
    $puesto = $_POST['puesto'];
    $salario = $_POST['salario'];
    $fecha_contratacion = $_POST['fecha_contratacion'];
    $telefono = $_POST['telefono'];

    $sql = "INSERT INTO empleados (nombre, puesto, salario, fecha_contratacion, telefono) 
            VALUES ('$nombre', '$puesto', '$salario', '$fecha_contratacion', '$telefono')";

    if ($conectar->query($sql) === TRUE) {
        $alerta = "Empleado registrado con éxito.";
        $tipo_alerta = "success";
    } else {
        $alerta = "Error al registrar el empleado: " . $conectar->error;
        $tipo_alerta = "danger";
    }
}

// Eliminar empleado
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM empleados WHERE id = $id";
    if ($conectar->query($sql) === TRUE) {
        $alerta = "Empleado eliminado con éxito.";
        $tipo_alerta = "success";
    } else {
        $alerta = "Error al eliminar el empleado: " . $conectar->error;
        $tipo_alerta = "danger";
    }
}

// Mostrar empleados al hacer clic en "Ver Empleados"
$ver_empleados = isset($_POST['ver_empleados']);

// Consulta para obtener todos los empleados
$consulta_empleados = "SELECT * FROM empleados ORDER BY nombre ASC";
$resultado_empleados = $conectar->query($consulta_empleados);

// Exportar empleados a Excel
if (isset($_POST['export_excel'])) {
    // Nombre del archivo
    $filename = "empleados_" . date('Y-m-d') . ".csv";
    
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
        'Puesto',
        'Salario',
        'Fecha de Contratación',
        'Teléfono'
    ));
    
    // Escribir los datos
    while ($data = $resultado_empleados->fetch_assoc()) {
        fputcsv($output, array(
            $data['id'],
            $data['nombre'],
            $data['puesto'],
            $data['salario'],
            $data['fecha_contratacion'],
            $data['telefono']
        ));
    }
    
    fclose($output);
    exit;
}
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
        
        $this->MultiCell(40, $height, $datos['puesto'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 90, $y);
        
        $this->MultiCell(30, $height, $datos['salario'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 120, $y);
        
        $this->MultiCell(30, $height, $datos['fecha_contratacion'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 150, $y);
        
        $this->MultiCell(30, $height, $datos['telefono'], 1, 'L');
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
    $pdf->Cell(0, 10, 'Reporte de Empleados', 0, 1, 'C');
    $pdf->Ln(5);
    // Encabezados de las columnas
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(10, 10, 'ID', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Nombre', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Puesto', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Salario', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Fecha Contrato', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Telefono', 1, 1, 'C');
    
    $pdf->SetFont('Arial', '', 11);
    // Mostrar los datos
    $consulta = "SELECT * FROM empleados ORDER BY nombre ASC";
    $resultado = $conectar->query($consulta);
    // Escribir los datos
    while($row = $resultado->fetch_assoc()) {
        $datos = array(
            'id' => $row['id'],
            'nombre' => utf8_decode($row['nombre']),
            'puesto' => utf8_decode($row['puesto']),
            'salario' => '$' . number_format($row['salario'], 2),
            'fecha_contratacion' => $row['fecha_contratacion'],
            'telefono' => $row['telefono']
        );
        $pdf->MultiCellRow($datos);
    }
    
    $pdf->Output('D', 'empleados_'.date('Y-m-d').'.pdf');
    exit;
}
?>