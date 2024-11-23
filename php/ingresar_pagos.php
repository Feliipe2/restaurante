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
    require 'tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $html = '<h1>Reporte de Pagos</h1><table border="1" cellpadding="5">
             <tr><th>ID</th><th>Cliente</th><th>Orden</th><th>Monto</th><th>Método de Pago</th></tr>';
    
    while ($row = $resultado_pagos->fetch_assoc()) {
        $html .= "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['cliente_nombre']}</td>
                    <td>{$row['orden_id']}</td>
                    <td>{$row['monto']}</td>
                    <td>{$row['metodo_pago']}</td>
                  </tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('pagos.pdf', 'D');
    exit;
}
?>