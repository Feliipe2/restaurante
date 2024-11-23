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
if (isset($_POST['export_pdf'])) {
    require 'tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $html = '<h1>Reporte de Reservas</h1><table border="1" cellpadding="5">
             <tr><th>ID</th><th>Nombre del Cliente</th><th>Fecha</th><th>Hora</th><th>Número de Personas</th><th>Teléfono</th><th>Estado</th></tr>';
    
    while ($row = $resultado_reservas->fetch_assoc()) {
        $html .= "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nombre_cliente']}</td>
                    <td>{$row['fecha']}</td>
                    <td>{$row['hora']}</td>
                    <td>{$row['numero_personas']}</td>
                    <td>{$row['telefono']}</td>
                    <td>{$row['estado']}</td>
                  </tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('reservas.pdf', 'D');
    exit;
}
?>