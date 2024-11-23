<?php
// Configuración de la conexión a la base de datos
$servidor = "localhost";
$nombreusuario = "root";
$password = "";
$bd = "restaurante";

// Conexión a la base de datos
$conn = mysqli_connect($servidor, $nombreusuario, $password, $bd);

// Verificamos si hay errores en la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para mensajes de alerta
$alerta = "";
$tipo_alerta = "";

// Consulta para obtener las categorías disponibles
$consulta_categorias = "SELECT * FROM categorias ORDER BY nombre_categoria ASC";
$resultado_categorias = $conn->query($consulta_categorias);

// Insertar nuevo plato en el menú
if (isset($_POST['agregar'])) {
    // Recibimos los datos del formulario
    $nombre_plato = $_POST['nombre_plato'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $id_categoria = $_POST['categoria'];
    $disponibilidad = isset($_POST['disponibilidad']) ? 1 : 0;

    // Consulta SQL para insertar el plato en la base de datos
    $sql = "INSERT INTO menu (nombre_plato, descripcion, precio, id_categoria, disponibilidad) 
            VALUES ('$nombre_plato', '$descripcion', '$precio', '$id_categoria', '$disponibilidad')";

    if ($conn->query($sql) === TRUE) {
        $alerta = "Nuevo plato agregado con éxito.";
        $tipo_alerta = "success";
    } else {
        $alerta = "Error al agregar el plato: " . $conn->error;
        $tipo_alerta = "danger";
    }
}

// Eliminar un plato del menú
if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM menu WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $alerta = "Plato eliminado con éxito.";
        $tipo_alerta = "success";
    } else {
        $alerta = "Error al eliminar el plato: " . $conn->error;
        $tipo_alerta = "danger";
    }
}

// Mostrar tabla solo si se hace clic en "Ver Menú"
$ver_menu = isset($_POST['ver_menu']);

// Consulta para obtener todos los platos del menú con sus categorías
$consulta = "SELECT menu.*, categorias.nombre_categoria AS categoria_nombre 
             FROM menu 
             LEFT JOIN categorias ON menu.id_categoria = categorias.id
             ORDER BY menu.id ASC";
$resultado_menu = $conn->query($consulta);

// Función para exportar el menú a CSV
if (isset($_POST['export_excel'])) {
    // Nombre del archivo
    $filename = "menu_" . date('Y-m-d') . ".csv";
    
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
        'Descripción',
        'Precio',
        'Categoría',
        'Disponibilidad',
    ));
    
    // Escribir los datos
    while ($data = $resultado_menu->fetch_assoc()) {
        fputcsv($output, array(
            $data['id'],
            $data['nombre_plato'],
            $data['descripcion'],
            $data['precio'],
            $data['categoria_nombre'],
            $data['disponibilidad'],
        ));
    }
    
    fclose($output);
    exit;
}
// Función para exportar el menú a PDF
if (isset($_POST['export_pdf'])) {
    require 'tcpdf.php';
    $pdf = new TCPDF();
    $pdf->AddPage();
    $html = '<h1>Reporte del Menú</h1><table border="1" cellpadding="5">
             <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Categoría</th></tr>';
    
    while ($row = $resultado_menu->fetch_assoc()) {
        $html .= "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['nombre_plato']}</td>
                    <td>{$row['descripcion']}</td>
                    <td>{$row['precio']}</td>
                    <td>{$row['categoria_nombre']}</td>
                  </tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('menu.pdf', 'D');
    exit;
}
?>