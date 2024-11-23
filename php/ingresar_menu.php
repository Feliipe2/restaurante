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
require('fpdf/fpdf.php');
class PDF extends FPDF {
    // Función para crear una celda con texto ajustable
    function MultiCellRow($datos) {
        $height = 6; // Altura mínima
        $x = $this->GetX();
        $y = $this->GetY();
        
        // Guardar la posición más alta
        $max_y = $y;
        
        // Primera columna - ID
        $this->MultiCell(20, $height, $datos['id'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 20, $y);
        
        // Segunda columna - Nombre
        $this->MultiCell(60, $height, $datos['nombre'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 80, $y);
        
        // Tercera columna - Descripción
        $this->MultiCell(90, $height, $datos['descripcion'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 170, $y);
        
        // Cuarta columna - Precio
        $this->MultiCell(30, $height, $datos['precio'], 1, 'R');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 200, $y);
        
        // Quinta columna - Categoría
        $this->MultiCell(50, $height, $datos['categoria'], 1, 'L');
        $max_y = max($max_y, $this->GetY());
        $this->SetXY($x + 250, $y);
        
        // Sexta columna - Disponibilidad
        $this->MultiCell(25, $height, $datos['disponible'], 1, 'C');
        $max_y = max($max_y, $this->GetY());
        
        // Establecer la posición para la siguiente fila
        $this->SetXY($x, $max_y);
    }
}

if (isset($_POST['export_pdf'])) {
    // Crear nuevo PDF
    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetMargins(10, 10, 10);
    
    // Título
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Menu del Restaurante', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Encabezados de la tabla
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(20, 10, 'ID', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Nombre', 1, 0, 'C');
    $pdf->Cell(90, 10, 'Descripcion', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Precio', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Categoria', 1, 0, 'C');
    $pdf->Cell(25, 10, 'Disponible', 1, 1, 'C');
    
    // Datos de la tabla
    $pdf->SetFont('Arial', '', 11);
    
    // Consulta para obtener los datos
    $consulta = "SELECT menu.*, categorias.nombre_categoria 
                 FROM menu 
                 LEFT JOIN categorias ON menu.id_categoria = categorias.id 
                 ORDER BY menu.id ASC";
    $resultado = $conn->query($consulta);
    
    while($row = $resultado->fetch_assoc()) {
        $datos = array(
            'id' => $row['id'],
            'nombre' => utf8_decode($row['nombre_plato']),
            'descripcion' => utf8_decode($row['descripcion']),
            'precio' => '$' . number_format($row['precio'], 2),
            'categoria' => utf8_decode($row['nombre_categoria']),
            'disponible' => $row['disponibilidad'] ? 'Si' : 'No'
        );
        $pdf->MultiCellRow($datos);
    }
    
    // Generar el PDF
    $pdf->Output('D', 'menu_'.date('Y-m-d').'.pdf');
    exit;
}
?>