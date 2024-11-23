<?php 
require '../includes/conexion.php';
include 'ingresar_reservas.php';
include 'editar_reserva.php';

// Verificar si la actualización de la reserva fue exitosa
$actualizacion_exitosa = isset($_GET['update']) && $_GET['update'] === 'success';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reservar Mesa</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="text-center my-4">Reservar Mesa</h1>

    <!-- Mostrar alerta si la actualización fue exitosa -->
    <?php if ($actualizacion_exitosa): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            La reserva se ha actualizado correctamente.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Formulario para reservar una mesa en el restaurante -->
    <form method="post" action="">
        <div class="form-group">
            <label for="nombre_cliente">Nombre del Cliente:</label>
            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
        </div>
        <div class="form-group">
            <label for="fecha">Fecha:</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>
        <div class="form-group">
            <label for="hora">Hora:</label>
            <input type="time" class="form-control" id="hora" name="hora" required>
        </div>
        <div class="form-group">
            <label for="personas">Número de Personas:</label>
            <input type="number" class="form-control" id="personas" name="personas" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado de la Reserva:</label>
            <select class="form-control" id="estado" name="estado">
                <option value="Pendiente">Pendiente</option>
                <option value="Confirmada">Confirmada</option>
                <option value="Cancelada">Cancelada</option>
                <option value="Completada">Completada</option>
            </select>
        </div>
        <button type="submit" name="agregar_reserva" class="btn btn-primary">
            <i class="fas fa-plus"></i> Agregar Reserva
        </button>
        <button type="reset" class="btn btn-warning">
            <i class="fas fa-undo"></i> Limpiar
        </button>
    </form>

    <!-- Botones para ver reservas y exportar en Excel y PDF -->
    <form method="post" action="" class="my-3">
        <button type="submit" name="ver_reservas" class="btn btn-info">
            <i class="fas fa-eye"></i> Ver Reservas
        </button>
        <button type="submit" name="export_excel" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
        <button type="submit" name="export_pdf" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </button>
    </form>

    <!-- Tabla para mostrar reservas solo si se ha hecho clic en "Ver Reservas" -->
    <?php if ($ver_reservas && $resultado_reservas): ?>
    <table class="table table-bordered table-hover mt-4">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nombre del Cliente</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Número de Personas</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado_reservas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre_cliente']; ?></td>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo $row['hora']; ?></td>
                    <td><?php echo $row['numero_personas']; ?></td>
                    <td><?php echo $row['telefono']; ?></td>
                    <td><?php echo $row['estado']; ?></td>
                    <td>
                        <!-- Botón para editar la reserva -->
                        <a href="#" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editReservationModal"
                           data-id="<?php echo $row['id']; ?>" data-nombre_cliente="<?php echo $row['nombre_cliente']; ?>" 
                           data-fecha="<?php echo $row['fecha']; ?>" data-hora="<?php echo $row['hora']; ?>" 
                           data-personas="<?php echo $row['numero_personas']; ?>" data-telefono="<?php echo $row['telefono']; ?>" 
                           data-estado="<?php echo $row['estado']; ?>">
                            <i class="fas fa-edit"></i> Editar
                        </a>

                        <!-- Botón para eliminar la reserva -->
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

<!-- Modal para editar reserva -->
<div class="modal fade" id="editReservationModal" tabindex="-1" role="dialog" aria-labelledby="editReservationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="editar_reserva.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReservationModalLabel">Editar Reserva</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-nombre_cliente">Nombre del Cliente:</label>
                        <input type="text" class="form-control" id="edit-nombre_cliente" name="nombre_cliente" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-fecha">Fecha:</label>
                        <input type="date" class="form-control" id="edit-fecha" name="fecha" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-hora">Hora:</label>
                        <input type="time" class="form-control" id="edit-hora" name="hora" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-personas">Número de Personas:</label>
                        <input type="number" class="form-control" id="edit-personas" name="numero_personas" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-telefono">Teléfono:</label>
                        <input type="text" class="form-control" id="edit-telefono" name="telefono" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-estado">Estado de la Reserva:</label>
                        <select class="form-control" id="edit-estado" name="estado">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Confirmada">Confirmada</option>
                            <option value="Cancelada">Cancelada</option>
                            <option value="Completada">Completada</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="actualizar_reserva" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery y Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    $('.edit-btn').on('click', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-nombre_cliente').val($(this).data('nombre_cliente'));
        $('#edit-fecha').val($(this).data('fecha'));
        $('#edit-hora').val($(this).data('hora'));
        $('#edit-personas').val($(this).data('personas'));
        $('#edit-telefono').val($(this).data('telefono'));
        $('#edit-estado').val($(this).data('estado'));
    });
});
</script>
</body>
</html>
