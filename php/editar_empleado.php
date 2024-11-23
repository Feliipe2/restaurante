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

// Actualizar empleado si se envía el formulario desde la modal
if (isset($_POST['actualizar_empleado'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $puesto = $_POST['puesto'];
    $salario = $_POST['salario'];
    $fecha_contratacion = $_POST['fecha_contratacion'];
    $telefono = $_POST['telefono'];

    // Consulta para actualizar los datos del empleado
    $sql = "UPDATE empleados SET nombre='$nombre', puesto='$puesto', salario='$salario', fecha_contratacion='$fecha_contratacion', telefono='$telefono' WHERE id='$id'";

    if ($conectar->query($sql) === TRUE) {
        header("Location: empleados.php?update=success");
        exit();
    } else {
        echo "Error al actualizar el empleado: " . $conectar->error;
    }
}
?>
