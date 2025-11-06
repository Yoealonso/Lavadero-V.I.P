<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include_once './Conexion/conexion.php';
include_once './Services/Interfaces/TurnoRepositoryInterface.php';
include_once './Services/Interfaces/NotificationServiceInterface.php';
include_once './Services/Interfaces/PrecioServiceInterface.php';
include_once './Services/Implementations/MySQLTurnoRepository.php';
include_once './Services/Implementations/WhatsAppNotificacionService.php';
include_once './Services/Implementations/DBPrecioService.php';
include_once './Services/TurnoService.php';

// Configuración
$whatsappConfig = [
    'api_url' => 'https://api.whatsapp.com/send',
    'token' => 'simulado',
    'numero_lavadero' => '2291416897'
];

// Mapeo de servicios del frontend
$servicios_mapeo = [
    'basico' => 'pre-venta-basic',
    'premium' => 'pre-venta-premium', 
    'full' => 'lavado-premium-auto',
    'tapizados' => 'limpieza-tapizados'
];

// Obtener servicio real (viene de vehiculo.php o directamente)
if (isset($_SESSION['servicio_redirigido'])) {
    $servicio_real = $_SESSION['servicio_redirigido'];
    unset($_SESSION['servicio_redirigido']);
} else {
    $servicio_solicitado = $_GET['servicio'] ?? 'pre-venta-basic';
    $servicio_real = $servicios_mapeo[$servicio_solicitado] ?? $servicio_solicitado;
}

// Array de servicios disponibles para validación
$servicios_disponibles = [
    'pre-venta-basic' => 'Pre Venta Basic',
    'pre-venta-premium' => 'Pre Venta Premium',
    'lavado-premium-auto' => 'Lavado Premium Auto',
    'lavado-premium-camioneta' => 'Lavado Premium Camioneta',
    'lavado-premium-suv' => 'Lavado Premium SUV', 
    'lavado-vip-extreme' => 'Lavado VIP Extreme',
    'tratamiento-ceramico' => 'Tratamiento Ceramico',
    'abrillantado-carroceria' => 'Abrillantado de Carroceria',
    'limpieza-motor' => 'Limpieza y Acondicionado de Motor',
    'pulido-opticas' => 'Pulido y Sellado de Opticas',
    'pintura-llantas' => 'Pintura de Llantas',
    'limpieza-tapizados' => 'Limpieza de Tapizados'
];

// Validar que el servicio existe
if (!array_key_exists($servicio_real, $servicios_disponibles)) {
    $servicio_real = 'pre-venta-basic';
}

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
        // Agregar el servicio real a los datos POST
        $_POST['servicio_real'] = $servicio_real;
        
        $reservaData = $turnoService->procesarSolicitudTurno($_POST);
        $successMessage = "Turno reservado correctamente. Te hemos enviado un WhatsApp para confirmar.";
        
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        error_log("Error en reserva: " . $e->getMessage());
    }
}

// Pasar el servicio a la vista
$_GET['servicio'] = $servicio_real;

include 'reserva_view.php';
?>