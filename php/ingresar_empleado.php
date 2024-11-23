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

// Exportar empleados a PDF
if (isset($_POST['export_pdf'])) {
    require 'tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $html = '<h1>Reporte de Empleados</h1><table border="1" cellpadding="5">
             <tr><th>ID</th><th>Nombre</th><th>Puesto</th><th>Salario</th><th>Fecha de Contratación</th><th>Teléfono</th></tr>';
    
    while ($row = $resultado_empleados->fetch_assoc()) {
        $html .= "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nombre']}</td>
                    <td>{$row['puesto']}</td>
                    <td>{$row['salario']}</td>
                    <td>{$row['fecha_contratacion']}</td>
                    <td>{$row['telefono']}</td>
                  </tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('empleados.pdf', 'D');
    exit;
}
?>