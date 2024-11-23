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
if (isset($_POST['export_pdf'])) {
    require 'tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $html = '<h1>Reporte de Órdenes</h1><table border="1" cellpadding="5">
             <tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Plato</th><th>Cantidad</th><th>Estado</th><th>Instrucciones</th></tr>';
    
    while ($row = $resultado_ordenes->fetch_assoc()) {
        $html .= "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nombre_cliente']}</td>
                    <td>{$row['fecha']}</td>
                    <td>{$row['nombre_plato']}</td>
                    <td>{$row['cantidad']}</td>
                    <td>{$row['estado']}</td>
                    <td>{$row['instrucciones']}</td>
                  </tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('ordenes.pdf', 'D');
    exit;
}
?>