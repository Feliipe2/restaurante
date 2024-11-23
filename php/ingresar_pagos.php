<?php
// Configuración de la conexión a la base de datos
$servidor = "localhost";
$nombreusuario = "root";
$password = "";
$bd = "restaurante";

$conn = mysqli_connect($servidor, $nombreusuario, $password, $bd);

// Verificamos si hay errores en la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para mensajes de alerta
$alerta = "";
$tipo_alerta = "";

// Registrar pago
if (isset($_POST['registrar_pago'])) {
    $id_cliente = $_POST['id_cliente'];
    $id_orden = $_POST['id_orden'];
    $monto = $_POST['monto'];
    $id_medio_pago = $_POST['metodo_pago'];

    // Insertar el pago en la base de datos
    $sql = "INSERT INTO pagos (id_cliente, id_orden, monto, id_medio_pago) 
            VALUES ('$id_cliente', '$id_orden', '$monto', '$id_medio_pago')";

    if ($conn->query($sql) === TRUE) {
        $alerta = "Pago registrado con éxito.";
        $tipo_alerta = "success";
    } else {
        $alerta = "Error al registrar el pago: " . $conn->error;
        $tipo_alerta = "danger";
    }
}

// Mostrar pagos al hacer clic en "Ver Pagos"
$ver_pagos = isset($_POST['ver_pagos']);

// Consulta para obtener todos los pagos con detalles del cliente y el método de pago
$consulta_pagos = "SELECT pagos.id, clientes.nombre AS cliente_nombre, ordenes.id AS orden_id, pagos.monto, medios_de_pago.nombre_medio AS metodo_pago 
                   FROM pagos 
                   JOIN clientes ON pagos.id_cliente = clientes.id 
                   JOIN ordenes ON pagos.id_orden = ordenes.id 
                   JOIN medios_de_pago ON pagos.id_medio_pago = medios_de_pago.id 
                   ORDER BY pagos.id ASC";
$resultado_pagos = $conn->query($consulta_pagos);

// Exportar pagos a Excel
if (isset($_POST['export_excel'])) {
    // Nombre del archivo
    $filename = "pagos_" . date('Y-m-d') . ".csv";
    
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
        'Orden',
        'Monto',
        'Método de Pago'
    ));
    
    // Escribir los datos
    while ($data = $resultado_pagos->fetch_assoc()) {
        fputcsv($output, array(
            $data['id'],
            $data['cliente_nombre'],
            $data['orden_id'],
            $data['monto'],
            $data['metodo_pago']
        ));
    }
    
    fclose($output);
    exit;
}

// Exportar pagos a PDF
if (isset($_POST['export_pdf'])) {
    require('fpdf/fpdf.php');
    class PDF extends FPDF {
        function MultiCellRow($datos) {
            $height = 6;
            $x = $this->GetX();
            $y = $this->GetY();
            $max_y = $y;

            $this->MultiCell(20, $height, $datos['id'], 1, 'L');
            $max_y = max($max_y, $this->GetY());
            $this->SetXY($x + 20, $y);

            $this->MultiCell(60, $height, $datos['cliente'], 1, 'L');
            $max_y = max($max_y, $this->GetY());
            $this->SetXY($x + 80, $y);

            $this->MultiCell(30, $height, $datos['orden'], 1, 'L');
            $max_y = max($max_y, $this->GetY());
            $this->SetXY($x + 110, $y);

            $this->MultiCell(30, $height, $datos['monto'], 1, 'R');
            $max_y = max($max_y, $this->GetY());
            $this->SetXY($x + 140, $y);

            $this->MultiCell(50, $height, $datos['metodo_pago'], 1, 'L');
            $max_y = max($max_y, $this->GetY());

            $this->SetXY($x, $max_y);
        }
    }

    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Reporte de Pagos', 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(20, 10, 'ID', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Cliente', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Orden', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Monto', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Metodo de Pago', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);

    $consulta = "SELECT pagos.id, clientes.nombre AS cliente_nombre, ordenes.id AS orden_id, pagos.monto, medios_de_pago.nombre_medio AS metodo_pago 
                 FROM pagos 
                 JOIN clientes ON pagos.id_cliente = clientes.id 
                 JOIN ordenes ON pagos.id_orden = ordenes.id 
                 JOIN medios_de_pago ON pagos.id_medio_pago = medios_de_pago.id 
                 ORDER BY pagos.id ASC";
    $resultado = $conn->query($consulta);

    while($row = $resultado->fetch_assoc()) {
        $datos = array(
            'id' => $row['id'],
            'cliente' => utf8_decode($row['cliente_nombre']),
            'orden' => $row['orden_id'],
            'monto' => '$' . number_format($row['monto'], 2),
            'metodo_pago' => utf8_decode($row['metodo_pago'])
        );
        $pdf->MultiCellRow($datos);
    }

    $pdf->Output('D', 'pagos_'.date('Y-m-d').'.pdf');
    exit;
}
?>