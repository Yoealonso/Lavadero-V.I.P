<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/Conexion/conexion.php';
include_once __DIR__ . '/Services/Interfaces/TurnoRepositoryInterface.php';
include_once __DIR__ . '/Services/Interfaces/NotificacionServiceInterface.php';
include_once __DIR__ . '/Services/Interfaces/PrecioServiceInterface.php';
include_once __DIR__ . '/Services/Implementations/MySQLTurnoRepository.php';
include_once __DIR__ . '/Services/Implementations/WhatsAppNotificacionService.php';
include_once __DIR__ . '/Services/Implementations/DBPrecioService.php';
include_once __DIR__ . '/Services/TurnoService.php';

// Configuración
$whatsappConfig = [
    'api_url' => 'https://api.whatsapp.com/send',
    'token' => 'simulado',
    'numero_lavadero' => '2291416897'
];

// Inicializar servicios con inyección de dependencias
$turnoRepository = new MySQLTurnoRepository($link);
$notificacionService = new WhatsAppNotificacionService(
    $whatsappConfig['api_url'], 
    $whatsappConfig['token'], 
    $whatsappConfig['numero_lavadero']
);
$precioService = new DBPrecioService($link);

$turnoService = new TurnoService($turnoRepository, $notificacionService, $precioService);

$successMessage = '';
$errorMessage = '';
$reservaData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $reservaData = $turnoService->procesarSolicitudTurno($_POST);
        $successMessage = "Turno reservado correctamente. Te hemos enviado un WhatsApp para confirmar.";
        
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        error_log("Error en reserva: " . $e->getMessage());
    }
}

include 'reserva_view.php';
?>