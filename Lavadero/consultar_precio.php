<?php
include_once __DIR__ . '/Conexion/conexion.php';
include_once __DIR__ . '/Services/Interfaces/PrecioServiceInterface.php';
include_once __DIR__ . '/Services/Implementations/DBPrecioService.php';

$precioService = new DBPrecioService($link);

// Obtener parámetros
$servicio = $_GET['servicio'] ?? '';
$tipo_vehiculo = $_GET['tipo'] ?? 'auto';
$plazas = $_GET['plazas'] ?? 4;

$precio = $precioService->calcularPrecio($servicio, $tipo_vehiculo, $plazas);

$nombres_servicios = [
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

$servicio_nombre = $nombres_servicios[$servicio] ?? 'Servicio';

// MENSAJE SIMPLIFICADO - Solo el servicio
$mensaje = "Hola! 👋\n";
$mensaje .= "Me gustaría consultar sobre el servicio: *{$servicio_nombre}*\n\n";
$mensaje .= "Precio de referencia: *$" . number_format($precio, 0, ',', '.') . "*\n\n";
$mensaje .= "Por favor, necesito:\n";
$mensaje .= "📅 Información sobre disponibilidad\n";
$mensaje .= "💰 Confirmación de precio exacto\n";
$mensaje .= "⏰ Tiempos de realización\n\n";
$mensaje .= "¡Muchas gracias! 🚗";

$numero_whatsapp = "2291416897";

$mensaje_codificado = urlencode($mensaje);

$url_whatsapp = "https://wa.me/{$numero_whatsapp}?text={$mensaje_codificado}";
header("Location: $url_whatsapp");
exit();
?>