<?php 
include 'ingresar_ordenes.php';
include 'editar_orden.php';  // Archivo para procesar la actualización de la orden

// Verificar si la actualización de la orden fue exitosa
$actualizacion_exitosa = isset($_GET['update']) && $_GET['update'] === 'success';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro de Órdenes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="text-center my-4">Registro de Órdenes</h1>

    <!-- Mostrar alerta si la actualización fue exitosa -->
    <?php if ($actualizacion_exitosa): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            La orden se ha actualizado correctamente.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Formulario de registro de órdenes -->
    <form method="post" action="">
        <div class="form-group">
            <label for="id_reserva">Reserva:</label>
            <select name="id_reserva" id="id_reserva" class="form-control">
                <?php
                $reservas = $conectar->query("SELECT * FROM reservas");
                while ($row = $reservas->fetch_assoc()):
                ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre_cliente'] . " - " . $row['fecha']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_menu">Plato:</label>
            <select name="id_menu" id="id_menu" class="form-control">
                <?php
                $menu = $conectar->query("SELECT * FROM menu");
                while ($row = $menu->fetch_assoc()):
                ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre_plato']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" class="form-control" id="cantidad" name="cantidad" required>
        </div>
        <div class="form-group">
            <label for="instrucciones">Instrucciones:</label>
            <textarea class="form-control" id="instrucciones" name="instrucciones"></textarea>
        </div>
        <button type="submit" name="agregar_orden" class="btn btn-primary">
            <i class="fas fa-check"></i> Registrar Orden
        </button>
        <button type="reset" class="btn btn-warning">
            <i class="fas fa-undo"></i> Limpiar
        </button>
    </form>

    <!-- Botones para ver órdenes y exportar en Excel y PDF -->
    <form method="post" action="" class="my-3">
        <button type="submit" name="ver_ordenes" class="btn btn-info">
            <i class="fas fa-eye"></i> Ver Órdenes
        </button>
        <button type="submit" name="export_excel" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
        <button type="submit" name="export_pdf" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </button>
    </form>

    <!-- Tabla para mostrar órdenes solo si se hace clic en "Ver Órdenes" -->
    <?php if ($ver_ordenes && $resultado_ordenes): ?>
    <table class="table table-bordered table-hover mt-4">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Plato</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Instrucciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado_ordenes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre_cliente']; ?></td>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo $row['nombre_plato']; ?></td>
                    <td><?php echo $row['cantidad']; ?></td>
                    <td><?php echo $row['estado']; ?></td>
                    <td><?php echo $row['instrucciones']; ?></td>
                    <td>
                        <!-- Botón para editar la orden -->
                        <a href="#" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editOrderModal"
                           data-id="<?php echo $row['id']; ?>" data-id_reserva="<?php echo $row['id_reserva']; ?>" 
                           data-id_menu="<?php echo $row['id_menu']; ?>" data-cantidad="<?php echo $row['cantidad']; ?>" 
                           data-instrucciones="<?php echo htmlspecialchars($row['instrucciones']); ?>">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        
                        <!-- Botón para eliminar la orden -->
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

<!-- Modal para editar orden -->
<div class="modal fade" id="editOrderModal" tabindex="-1" role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="editar_orden.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOrderModalLabel">Editar Orden</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-id_reserva">Reserva:</label>
                        <select name="id_reserva" id="edit-id_reserva" class="form-control">
                            <?php
                            $reservas = $conectar->query("SELECT * FROM reservas");
                            while ($row = $reservas->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre_cliente'] . " - " . $row['fecha']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-id_menu">Plato:</label>
                        <select name="id_menu" id="edit-id_menu" class="form-control">
                            <?php
                            $menu = $conectar->query("SELECT * FROM menu");
                            while ($row = $menu->fetch_assoc()):
                            ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre_plato']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-cantidad">Cantidad:</label>
                        <input type="number" class="form-control" id="edit-cantidad" name="cantidad" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-instrucciones">Instrucciones:</label>
                        <textarea class="form-control" id="edit-instrucciones" name="instrucciones"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" name="actualizar_orden" class="btn btn-primary">Guardar Cambios</button>
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
        $('#edit-id_reserva').val($(this).data('id_reserva'));
        $('#edit-id_menu').val($(this).data('id_menu'));
        $('#edit-cantidad').val($(this).data('cantidad'));
        $('#edit-instrucciones').val($(this).data('instrucciones'));
    });
});
</script>
</body>
</html>
