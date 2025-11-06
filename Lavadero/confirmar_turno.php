<?php
include_once __DIR__ . '/Conexion/conexion.php';
include_once __DIR__ . '/Services/Interfaces/TurnoRepositoryInterface.php';
include_once __DIR__ . '/Services/Interfaces/NotificacionServiceInterface.php';
include_once __DIR__ . '/Services/Implementations/MySQLTurnoRepository.php';
include_once __DIR__ . '/Services/Implementations/WhatsAppNotificacionService.php';

// Configuración
$whatsappConfig = [
    'api_url' => 'https://api.whatsapp.com/send',
    'token' => 'simulado',
    'numero_lavadero' => '2291416897'
];

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Token inválido");
}

// Inicializar servicios
$turnoRepository = new MySQLTurnoRepository($link);
$notificacionService = new WhatsAppNotificacionService(
    $whatsappConfig['api_url'], 
    $whatsappConfig['token'], 
    $whatsappConfig['numero_lavadero']
);

// Buscar y confirmar turno
$turno = $turnoRepository->obtenerTurnoPorToken($token);

if (!$turno) {
    die("Turno no encontrado o ya confirmado");
}

if ($turnoRepository->confirmarTurno($token)) {
    // Enviar confirmación al lavadero
    $notificacionService->enviarConfirmacionLavadero([
        'cliente' => $turno['nombre'] . ' ' . $turno['apellido'],
        'telefono' => $turno['telefono'],
        'servicio' => $turno['servicio_nombre'],
        'vehiculo' => $turno['marca'] . ' ' . $turno['modelo'],
        'patente' => $turno['patente'],
        'fecha' => date('d/m/Y', strtotime($turno['fechaReserva'])),
        'hora' => $turno['horaReserva'],
        'precio' => $turno['precio_final']
    ]);
    
    $mensajeExito = "Turno confirmado exitosamente";
} else {
    $mensajeExito = "El turno ya estaba confirmado";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Turno Confirmado - VIP CAR WASH</title>
    <link rel='stylesheet' href='style.css'>
</head>
<body>
    <header>
        <div class="logo"><h2>VIP CAR WASH</h2></div>
    </header>

    <nav>
        <a href="index.html" style="margin-right: 40px;" class="na"> Inicio</a>
        <a href="#servicios" style="margin-right: 40px;" class="na"> Servicios</a>
        <a href="panel.php" class="admin-btn"> Admin</a>
    </nav>

    <div class="reserva-container">
        <div class="alert-modern alert-success">
            <h2>✅ Turno Confirmado</h2>
            <p>Tu turno ha sido confirmado exitosamente.</p>
            <p><strong>Cliente:</strong> <?= htmlspecialchars($turno['nombre'] . ' ' . $turno['apellido']) ?></p>
            <p><strong>Vehículo:</strong> <?= htmlspecialchars($turno['marca'] . ' ' . $turno['modelo']) ?> (<?= htmlspecialchars($turno['patente']) ?>)</p>
            <p><strong>Servicio:</strong> <?= htmlspecialchars($turno['servicio_nombre']) ?></p>
            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($turno['fechaReserva'])) ?></p>
            <p><strong>Hora:</strong> <?= htmlspecialchars($turno['horaReserva']) ?></p>
            <p><strong>Precio:</strong> $<?= number_format($turno['precio_final'], 0, ',', '.') ?></p>
            <a href="index.html" class="reserva-btn">Volver al Inicio</a>
        </div>
    </div>
</body>
</html>