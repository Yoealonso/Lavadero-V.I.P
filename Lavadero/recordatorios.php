<?php
include_once __DIR__ . '/Conexion/conexion.php';
include_once __DIR__ . '/Services/Implementations/WhatsAppNotificacionService.php';

// Configuración
$whatsappConfig = [
    'api_url' => 'https://api.whatsapp.com/send',
    'token' => 'simulado', 
    'numero_lavadero' => '2291416897'
];

$notificacionService = new WhatsAppNotificacionService(
    $whatsappConfig['api_url'],
    $whatsappConfig['token'],
    $whatsappConfig['numero_lavadero']
);

// Buscar turnos confirmados para mañana que no han recibido recordatorio
$fechaManana = date('Y-m-d', strtotime('+1 day'));

$query = "SELECT t.*, c.nombre, c.apellido, c.telefono, v.marca, v.modelo, v.patente, s.nombre as servicio_nombre
        FROM turno t
        JOIN cliente c ON t.idcliente = c.idcliente
        JOIN vehiculo v ON t.idvehiculo = v.idvehiculo  
        JOIN servicio s ON t.idservicio = s.idservicio
        WHERE t.fechaReserva = ? 
        AND t.estado = 'confirmado'
        AND t.recordatorio_enviado = 0";

$stmt = $link->prepare($query);
$stmt->bind_param("s", $fechaManana);
$stmt->execute();
$result = $stmt->get_result();

$enviados = 0;
while ($turno = $result->fetch_assoc()) {
    // Enviar recordatorio al cliente
    $mensajeCliente = $this->construirRecordatorioCliente($turno);
    $notificacionService->enviarMensaje($turno['telefono'], $mensajeCliente);
    
    // Enviar notificación al lavadero
    $mensajeLavadero = $this->construirRecordatorioLavadero($turno);
    $notificacionService->enviarMensaje($whatsappConfig['numero_lavadero'], $mensajeLavadero);
    
    // Marcar como enviado
    $updateStmt = $link->prepare("UPDATE turno SET recordatorio_enviado = 1 WHERE idturno = ?");
    $updateStmt->bind_param("i", $turno['idturno']);
    $updateStmt->execute();
    $updateStmt->close();
    
    $enviados++;
}

$stmt->close();

echo "Recordatorios enviados: " . $enviados;

function construirRecordatorioCliente($turno) {
    return "🔔 Recordatorio VIP CAR WASH\n\n" .
        "Hola {$turno['nombre']},\n\n" .
        "Te recordamos tu turno para mañana:\n" .
        "📅 Fecha: " . date('d/m/Y', strtotime($turno['fechaReserva'])) . "\n" .
        "⏰ Hora: {$turno['horaReserva']}\n" .
        "🚗 Servicio: {$turno['servicio_nombre']}\n" .
        "Vehículo: {$turno['marca']} {$turno['modelo']} ({$turno['patente']})\n\n" .
        "📍 Dirección: Calle 19 numero 1676, Miramar\n\n" .
        "¡Te esperamos! 🚗✨";
}

function construirRecordatorioLavadero($turno) {
    return "📋 RECORDATORIO TURNO MAÑANA\n\n" .
        "Cliente: {$turno['nombre']} {$turno['apellido']}\n" .
        "Teléfono: {$turno['telefono']}\n" .
        "Servicio: {$turno['servicio_nombre']}\n" .
        "Vehículo: {$turno['marca']} {$turno['modelo']} ({$turno['patente']})\n" .
        "Hora: {$turno['horaReserva']}\n\n" .
        "Estado: Confirmado ✅";
}
?>