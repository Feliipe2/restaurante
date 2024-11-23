<?php
include 'ingresar_pagos.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro de Pagos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script>
        // Script para ocultar automáticamente las alertas después de 5 segundos
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                const alert = document.querySelector(".alert");
                if (alert) {
                    alert.style.display = "none";
                }
            }, 5000);
        });
    </script>
</head>
<body>
<div class="container">
    <h1 class="text-center my-4">Registro de Pagos</h1>

    <!-- Mostrar alerta si hay un mensaje -->
    <?php if ($alerta): ?>
        <div class="alert alert-<?= $tipo_alerta ?> alert-dismissible fade show" role="alert">
            <?= $alerta ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Formulario para registrar el pago de una orden -->
    <form method="post" action="">
        <div class="form-group">
            <label for="id_cliente">Cliente:</label>
            <select name="id_cliente" id="id_cliente" class="form-control" required>
                <?php
                // Consulta para obtener los clientes
                $clientes = $conn->query("SELECT * FROM clientes");
                while ($row = $clientes->fetch_assoc()):
                ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_orden">Orden:</label>
            <select name="id_orden" id="id_orden" class="form-control" required>
                <?php
                // Consulta para obtener las órdenes pendientes
                $ordenes = $conn->query("SELECT * FROM ordenes WHERE estado='Pendiente'");
                while ($row = $ordenes->fetch_assoc()):
                ?>
                    <option value="<?php echo $row['id']; ?>">Orden #<?php echo $row['id']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="monto">Monto:</label>
            <input type="number" class="form-control" id="monto" name="monto" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="metodo_pago">Método de Pago:</label>
            <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                <?php
                // Consulta para obtener los medios de pago
                $medios_pago = $conn->query("SELECT * FROM medios_de_pago");
                while ($row = $medios_pago->fetch_assoc()):
                ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre_medio']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="registrar_pago" class="btn btn-primary">
            <i class="fas fa-check"></i> Registrar Pago
        </button>
        <button type="reset" class="btn btn-warning">
            <i class="fas fa-undo"></i> Limpiar
        </button>
    </form>

    <!-- Botones para ver pagos y exportar en Excel y PDF -->
    <form method="post" action="" class="my-3">
        <button type="submit" name="ver_pagos" class="btn btn-info">
            <i class="fas fa-eye"></i> Ver Pagos
        </button>
        <button type="submit" name="export_excel" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </button>
        <button type="submit" name="export_pdf" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </button>
    </form>

    <!-- Tabla para mostrar pagos solo si se ha hecho clic en "Ver Pagos" -->
    <?php if ($ver_pagos): ?>
    <table class="table table-bordered table-hover mt-4">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Orden</th>
                <th>Monto</th>
                <th>Método de Pago</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado_pagos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['cliente_nombre']; ?></td>
                    <td><?php echo $row['orden_id']; ?></td>
                    <td><?php echo $row['monto']; ?></td>
                    <td><?php echo $row['metodo_pago']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html>
