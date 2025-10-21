<?php
// panel.php - Panel de administración
include_once("C:/xampp/htdocs/Lavadero/Conexion/conexion.php");

// Obtener turnos para mostrar
$query = "SELECT t.*, c.nombre, c.apellido, c.telefono, v.marca, v.modelo, v.patente, v.color, s.nombre as servicio 
          FROM turno t 
          JOIN cliente c ON t.idcliente = c.idcliente 
          JOIN vehiculo v ON t.idvehiculo = v.idvehiculo 
          JOIN servicio s ON t.idservicio = s.idservicio 
          ORDER BY t.fechaReserva DESC, t.horaReserva DESC";
$result = mysqli_query($link, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel Admin - VIP CAR WASH</title>
    <link rel='stylesheet' href='style.css'>
    <style>
        .estado-pendiente { background-color: #fff3cd; }
        .estado-confirmado { background-color: #d1edff; }
        .estado-cancelado { background-color: #f8d7da; }
        .estado-completado { background-color: #d4edda; }
    </style>
</head>
<body>
    <header><h2>PANEL ADMINISTRADOR - VIP CAR WASH</h2></header>
    
    <div style="padding: 20px;">
        <h3>📋 Turnos Programados</h3>
        
        <table border="1" style="width:100%; border-collapse:collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #0077b6; color: white;">
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Vehículo</th>
                    <th>Color</th>
                    <th>Servicio</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Confirmado</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): 
                    $estadoClass = 'estado-' . $row['estado'];
                    $confirmado = $row['fecha_confirmacion'] ? date('d/m/Y H:i', strtotime($row['fecha_confirmacion'])) : 'No';
                ?>
                <tr class="<?= $estadoClass ?>">
                    <td><?= date('d/m/Y', strtotime($row['fechaReserva'])) ?></td>
                    <td><?= $row['horaReserva'] ?></td>
                    <td><?= htmlspecialchars($row['nombre']) ?> <?= htmlspecialchars($row['apellido']) ?></td>
                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                    <td><?= htmlspecialchars($row['marca']) ?> <?= htmlspecialchars($row['modelo']) ?> (<?= htmlspecialchars($row['patente']) ?>)</td>
                    <td><?= htmlspecialchars($row['color']) ?></td>
                    <td><?= htmlspecialchars($row['servicio']) ?></td>
                    <td>$<?= number_format($row['precio_final'], 0, ',', '.') ?></td>
                    <td><strong><?= strtoupper($row['estado']) ?></strong></td>
                    <td><?= $confirmado ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <?php if(mysqli_num_rows($result) === 0): ?>
            <p style="text-align: center; padding: 20px; color: #666;">No hay turnos programados.</p>
        <?php endif; ?>
    </div>
</body>
</html>