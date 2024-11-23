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

// Actualizar cliente si se envía el formulario desde la modal
if (isset($_POST['actualizar_cliente'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    // Consulta para actualizar los datos del cliente
    $sql = "UPDATE clientes SET nombre='$nombre', email='$email', telefono='$telefono', direccion='$direccion' WHERE id='$id'";

    if ($conectar->query($sql) === TRUE) {
        header("Location: clientes.php?update=success");
        exit();
    } else {
        echo "Error al actualizar el cliente: " . $conectar->error;
    }
}
?>
