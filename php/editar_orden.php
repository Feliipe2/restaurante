<?php
require '../includes/conexion.php';

// Cargar órdenes solo si se hace clic en "Ver Órdenes"
$ver_ordenes = isset($_POST['ver_ordenes']);
if ($ver_ordenes) {
    $consulta_ordenes = "
        SELECT ordenes.id, reservas.nombre_cliente, reservas.fecha, menu.nombre_plato, ordenes.cantidad, ordenes.estado, ordenes.instrucciones,
               ordenes.id_reserva, ordenes.id_menu
        FROM ordenes
        JOIN reservas ON ordenes.id_reserva = reservas.id
        JOIN menu ON ordenes.id_menu = menu.id
        ORDER BY reservas.fecha ASC";
    $resultado_ordenes = $conectar->query($consulta_ordenes);
}

// Actualizar la orden cuando se envían datos desde la modal
if (isset($_POST['actualizar_orden'])) {
    $id = $_POST['id'];
    $id_reserva = $_POST['id_reserva'];
    $id_menu = $_POST['id_menu'];
    $cantidad = $_POST['cantidad'];
    $instrucciones = $_POST['instrucciones'];

    $sql = "UPDATE ordenes SET id = '$id_reserva', id = '$id_menu', cantidad = '$cantidad', instrucciones = '$instrucciones' WHERE id = '$id'";

    if ($conectar->query($sql) === TRUE) {
        header("Location: ordenes.php?update=success");
        exit;
    } else {
        echo "Error al actualizar la orden: " . $conectar->error;
    }
}
?>
