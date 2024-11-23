<?php
include 'ingresar_cliente.php';
include 'editar_cliente.php';

// Mensaje de éxito al actualizar
$actualizacion_exitosa = isset($_GET['update']) && $_GET['update'] === 'success';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro de Clientes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="text-center my-4">Registro de Clientes</h1>

    <!-- Mostrar alerta si la actualización fue exitosa -->
    <?php if ($actualizacion_exitosa): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            La información del cliente se ha actualizado correctamente.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Mostrar alerta si hay un mensaje -->
    <?php if ($alerta): ?>
        <div class="alert alert-<?= $tipo_alerta ?> alert-dismissible fade show" role="alert">
            <?= $alerta ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar un nuevo cliente -->
    <form method="post" action="">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" class="form-control" id="direccion" name="direccion">
        </div>
        <button type="submit" name="agregar_cliente" class="btn btn-primary">
            <i class="fas fa-check"></i> Registrar Cliente
        </button>
        <button type="reset" class="btn btn-warning">
            <i class="fas fa-undo"></i> Limpiar
        </button>
    </form>

    <!-- Botones para ver clientes y exportar en Excel y PDF -->
    <form method="post" action="" class="my-3">
        <button type="submit" name="ver_clientes" class="btn btn-info">
            <i class="fas fa-eye"></i> Ver Clientes
        </button>
        <button type="submit" name="export_excel" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
        <button type="submit" name="export_pdf" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </button>
    </form>

    <!-- Tabla para mostrar los clientes solo si se ha hecho clic en "Ver Clientes" -->
    <?php if ($ver_clientes): ?>
    <table class="table table-bordered table-hover mt-4">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado_clientes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['telefono']; ?></td>
                    <td><?php echo $row['direccion']; ?></td>
                    <td>
                        <!-- Botón para editar el cliente, que abre la modal con los datos -->
                        <a href="#" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editClientModal" 
                           data-id="<?php echo $row['id']; ?>" data-nombre="<?php echo $row['nombre']; ?>" 
                           data-email="<?php echo $row['email']; ?>" data-telefono="<?php echo $row['telefono']; ?>" 
                           data-direccion="<?php echo $row['direccion']; ?>">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        
                        <!-- Botón para eliminar el cliente -->
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="eliminar" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- Modal para editar cliente -->
<div class="modal fade" id="editClientModal" tabindex="-1" role="dialog" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="editar_cliente.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClientModalLabel">Editar Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-nombre">Nombre:</label>
                        <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email:</label>
                        <input type="email" class="form-control" id="edit-email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="edit-telefono">Teléfono:</label>
                        <input type="text" class="form-control" id="edit-telefono" name="telefono" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-direccion">Dirección:</label>
                        <input type="text" class="form-control" id="edit-direccion" name="direccion">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="actualizar_cliente" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery y Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Script para cargar los datos en la modal -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    $('.edit-btn').on('click', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-nombre').val($(this).data('nombre'));
        $('#edit-email').val($(this).data('email'));
        $('#edit-telefono').val($(this).data('telefono'));
        $('#edit-direccion').val($(this).data('direccion'));
    });
});
</script>
</body>
</html>
