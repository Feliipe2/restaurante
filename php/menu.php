<?php 
include 'ingresar_menu.php'; 
include 'editar_menu.php';

// Mensaje de éxito al actualizar
$actualizacion_exitosa = isset($_GET['update']) && $_GET['update'] === 'success';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Menú - Restaurante</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4 text-center">Menú del Restaurante</h1>

        <!-- Mostrar alerta si la actualización fue exitosa -->
        <?php if ($actualizacion_exitosa): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                La información del plato se ha actualizado correctamente.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Formulario para agregar un nuevo plato al menú -->
        <form method="post" action="">
            <h3>Agregar Nuevo Plato</h3>
            <div class="form-group">
                <label for="nombre_plato">Nombre del Plato:</label>
                <input type="text" class="form-control" id="nombre_plato" name="nombre_plato" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <select class="form-control" id="categoria" name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <?php while ($row = $resultado_categorias->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre_categoria']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="disponibilidad" name="disponibilidad">
                <label class="form-check-label" for="disponibilidad">Disponible</label>
            </div>
            <button type="submit" name="agregar" class="btn btn-primary">
                <i class="fas fa-plus"></i> Agregar Plato
            </button>
            <button type="reset" class="btn btn-warning">
                <i class="fas fa-undo"></i> Limpiar
            </button>
        </form>

        <!-- Botones para ver el menú, exportar en Excel y PDF -->
        <form method="post" action="" class="my-3">
            <button type="submit" name="ver_menu" class="btn btn-info">
                <i class="fas fa-eye"></i> Ver Menú
            </button>
            <button type="submit" name="export_excel" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
            <button type="submit" name="export_pdf" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </button>
        </form>

        <!-- Tabla para mostrar el menú solo si se ha hecho clic en "Ver Menú" -->
        <?php if ($ver_menu): ?>
        <table class="table table-bordered table-hover mt-4">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Disponibilidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultado_menu->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nombre_plato']; ?></td>
                        <td><?php echo $row['descripcion']; ?></td>
                        <td><?php echo $row['precio']; ?></td>
                        <td><?php echo $row['categoria_nombre']; ?></td>
                        <td><?php echo $row['disponibilidad'] ? "Sí" : "No"; ?></td>
                        <td>
                            <!-- Botón para editar el plato, que abre la modal con los datos -->
                            <a href="#" class="btn btn-sm btn-primary edit-btn" data-toggle="modal" data-target="#editMenuModal" 
                               data-id="<?php echo $row['id']; ?>" data-nombre_plato="<?php echo $row['nombre_plato']; ?>" 
                               data-descripcion="<?php echo $row['descripcion']; ?>" data-precio="<?php echo $row['precio']; ?>" 
                               data-categoria_id="<?php echo $row['id_categoria']; ?>">
                                <i class="fas fa-edit"></i> Editar
                            </a><br><br>

                            <!-- Botón para eliminar el plato -->
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

    <!-- Modal para editar plato -->
    <div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog" aria-labelledby="editMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="editar_menu.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMenuModalLabel">Editar Plato</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label for="edit-nombre_plato">Nombre del Plato:</label>
                            <input type="text" class="form-control" id="edit-nombre_plato" name="nombre_plato" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-descripcion">Descripción:</label>
                            <textarea class="form-control" id="edit-descripcion" name="descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-precio">Precio:</label>
                            <input type="number" class="form-control" id="edit-precio" name="precio" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-categoria">Categoría:</label>
                            <select class="form-control" id="edit-categoria" name="categoria" required>
                                <?php
                                // Recargamos las categorías para la modal
                                $resultado_categorias = $conectar->query("SELECT * FROM categorias");
                                while ($categoria = $resultado_categorias->fetch_assoc()):
                                ?>
                                    <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['nombre_categoria']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="edit-disponibilidad" name="disponibilidad">
                            <label class="form-check-label" for="edit-disponibilidad">Disponible</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" name="actualizar_plato" class="btn btn-primary">Guardar Cambios</button>
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
            $('#edit-nombre_plato').val($(this).data('nombre_plato'));
            $('#edit-descripcion').val($(this).data('descripcion'));
            $('#edit-precio').val($(this).data('precio'));
            $('#edit-categoria').val($(this).data('categoria_id'));
            $('#edit-disponibilidad').prop('checked', $(this).data('disponibilidad') == 1);
        });
    });
    </script>
</body>
</html>
