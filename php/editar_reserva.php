<?php
require '../includes/conexion.php';

// Verificar si el formulario de edición de reserva fue enviado
if (isset($_POST['actualizar_reserva'])) {
    $id = $_POST['id'];
    $nombre_cliente = $_POST['nombre_cliente'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $numero_personas = $_POST['numero_personas'];
    $telefono = $_POST['telefono'];
    $estado = $_POST['estado'];

    // Consulta para actualizar la reserva en la base de datos
    $sql = "UPDATE reservas 
            SET nombre_cliente = '$nombre_cliente', fecha = '$fecha', hora = '$hora', numero_personas = '$numero_personas', telefono = '$telefono', estado = '$estado' 
            WHERE id = $id";

    if ($conectar->query($sql) === TRUE) {
        // Redireccionar a la página de reservas con mensaje de éxito
        header("Location: reservas.php?update=success");
    } else {
        echo "Error al actualizar la reserva: " . $conectar->error;
    }
}

// Cerrar la conexión
$conectar->close();
?>
