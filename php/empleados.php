<?php
include 'ingresar_empleado.php';
include 'editar_empleado.php';

// Verificar si se ha actualizado correctamente para mostrar la alerta
$actualizacion_exitosa = isset($_GET['update']) && $_GET['update'] === 'success';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro de Empleados</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="text-center my-4">Registro de Empleados</h1>

    <!-- Mostrar alerta si la actualización fue exitosa -->
    <?php if ($actualizacion_exitosa): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            La información del empleado se ha actualizado correctamente.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Formulario para agregar un nuevo empleado -->
    <form method="post" action="">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="puesto">Puesto:</label>
            <input type="text" class="form-control" id="puesto" name="puesto" required>
        </div>
        <div class="form-group">
            <label for="salario">Salario:</label>
            <input type="number" class="form-control" id="salario" name="salario" required>
        </div>
        <div class="form-group">
            <label for="fecha_contratacion">Fecha de Contratación:</label>
            <input type="date" class="form-control" id="fecha_contratacion" name="fecha_contratacion" required>
        </div>
        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <button type="submit" name="agregar_empleado" class="btn btn-primary">
            <i class="fas fa-check"></i> Registrar Empleado
        </button>
        <button type="reset" class="btn btn-warning">
            <i class="fas fa-undo"></i> Limpiar
        </button>
    </form>

    <!-- Botones para ver empleados y exportar en Excel y PDF -->
    <form method="post" action="" class="my-3">
        <button type="submit" name="ver_empleados" class="btn btn-info">
            <i class="fas fa-eye"></i> Ver Empleados
        </button>
        <button type="submit" name="export_excel" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
        <button type="submit" name="export_pdf" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </button>
    </form>

    <!-- Tabla para mostrar los empleados solo si se ha hecho clic en "Ver Empleados" -->
    <?php if ($ver_empleados): ?>
    <table class="table table-bordered table-hover mt-4">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Puesto</th>
                <th>Salario</th>
                <th>Fecha de Contratación</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado_empleados->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['puesto']; ?></td>
                    <td><?php echo $row['salario']; ?></td>
                    <td><?php echo date('Y-m-d', strtotime($row['fecha_contratacion'])); ?></td>
                    <td><?php echo $row['telefono']; ?></td>
                    <td>
                        <!-- Botón para editar el empleado, que abre la modal con los datos -->
                        <a href="#" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editEmployeeModal" 
                           data-id="<?php echo $row['id']; ?>" data-nombre="<?php echo $row['nombre']; ?>" 
                           data-puesto="<?php echo $row['puesto']; ?>" data-salario="<?php echo $row['salario']; ?>" 
                           data-fecha_contratacion="<?php echo date('Y-m-d', strtotime($row['fecha_contratacion'])); ?>" data-telefono="<?php echo $row['telefono']; ?>">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        
                        <!-- Botón para eliminar el empleado -->
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

<!-- Modal para editar empleado -->
<div class="modal fade" id="editEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="editar_empleado.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Editar Empleado</h5>
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
                        <label for="edit-puesto">Puesto:</label>
                        <input type="text" class="form-control" id="edit-puesto" name="puesto" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-salario">Salario:</label>
                        <input type="number" class="form-control" id="edit-salario" name="salario" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-fecha">Fecha de Contratación:</label>
                        <input type="date" class="form-control" id="edit-fecha" name="fecha_contratacion" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-telefono">Teléfono:</label>
                        <input type="text" class="form-control" id="edit-telefono" name="telefono" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="actualizar_empleado" class="btn btn-primary">Guardar Cambios</button>
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
        $('#edit-nombre').val($(this).data('nombre'));
        $('#edit-puesto').val($(this).data('puesto'));
        $('#edit-salario').val($(this).data('salario'));
        $('#edit-fecha').val($(this).data('fecha_contratacion'));
        $('#edit-telefono').val($(this).data('telefono'));
    });
});
</script>
</body>
</html>
