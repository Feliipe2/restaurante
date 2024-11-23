<?php
// Conexión a la base de datos del restaurante
$servidor = "localhost";
$nombreusuario = "root";
$password = "";
$bd = "restaurante";

$conectar = mysqli_connect($servidor, $nombreusuario, $password, $bd);

// Validamos que la conexión esté bien
if ($conectar->connect_error) {
    die("Conexión fallida: " . $conectar->connect_error);
}
?>