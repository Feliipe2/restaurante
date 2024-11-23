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
if (isset($_POST['export_pdf'])) {
    require 'tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $html = '<h1>Reporte de Clientes</h1><table border="1" cellpadding="5">
             <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Dirección</th></tr>';
    
    while ($row = $resultado_clientes->fetch_assoc()) {
        $html .= "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nombre']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['telefono']}</td>
                    <td>{$row['direccion']}</td>
                  </tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('clientes.pdf', 'D');
    exit;
}
?>