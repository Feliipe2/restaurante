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

// Actualizar plato si se envía el formulario desde la modal
if (isset($_POST['actualizar_plato'])) {
    $id = $_POST['id'];
    $nombre_plato = $_POST['nombre_plato'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $disponibilidad = isset($_POST['disponibilidad']) ? 1 : 0;

    // Consulta para actualizar los datos del plato
    $sql = "UPDATE menu SET nombre_plato='$nombre_plato', descripcion='$descripcion', precio='$precio', categoria='$categoria', disponibilidad='$disponibilidad' WHERE id='$id'";

    if ($conectar->query($sql) === TRUE) {
        header("Location: menu.php?update=success");
        exit();
    } else {
        echo "Error al actualizar el plato: " . $conectar->error;
    }
}
?>
