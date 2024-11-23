<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Inicio - Restaurante</title>
    <!-- Bootstrap y Font Awesome para darle estilo a los botones e íconos -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Contenedor principal para centrar el contenido -->
    <div class="d-flex justify-content-center">
        <img src="img/icono.png" alt="Imagen centrada" class="img-fluid" style="width: 220px; height: 220px; margin-top:6em;">
    </div>
    <div class="container text-center">
        <h1 class="my-4">Restaurante Los Tres Mosqueteros</h1>
        <h2>Bienvenido</h2>

        <!-- Lista de enlaces hacia las secciones del sistema -->
        <div class="list-group my-4">
            <a href="php/menu.php" class="list-group-item list-group-item-action">Menú</a>
            <a href="php/clientes.php" class="list-group-item list-group-item-action">Clientes</a>
            <a href="php/reservas.php" class="list-group-item list-group-item-action">Reservas</a>
            <a href="php/ordenes.php" class="list-group-item list-group-item-action">Órdenes</a>
            <a href="php/empleados.php" class="list-group-item list-group-item-action">Empleados</a>
            <a href="php/pagos.php" class="list-group-item list-group-item-action">Pagos</a>
        </div>
    </div>
</body>
</html>
