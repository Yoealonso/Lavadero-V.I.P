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

// Mapeo completo de todos los servicios
$nombres_servicios = [
    // Servicios de auto desde BD
    'pre-venta-basic' => 'Pre Venta Basic',
    'pre-venta-premium' => 'Pre Venta Premium',
    'lavado-premium-auto' => 'Lavado Premium Auto',
    'lavado-premium-camioneta' => 'Lavado Premium Camioneta',
    'lavado-premium-suv' => 'Lavado Premium SUV',
    'lavado-vip-extreme' => 'Lavado VIP Extreme',
    'tratamiento-ceramico' => 'Tratamiento Cerámico',
    'abrillantado-carroceria' => 'Abrillantado de Carrocería',
    'limpieza-motor' => 'Limpieza y Acondicionado de Motor',
    'pulido-opticas' => 'Pulido y Sellado de Ópticas',
    'pintura-llantas' => 'Pintura de Llantas',
    'limpieza-tapizados' => 'Limpieza de Tapizados',
    
    // Otros servicios (productos y servicios para el hogar)
    'alfombras-ziel' => 'Alfombras Termoformadas ZIEL',
    'limpieza-colchones' => 'Limpieza de Colchones',
    'limpieza-sillones' => 'Limpieza de Sillones',
    'lampara-led-ir' => 'Lámparas LED IR 100',
    'lampara-led-r8' => 'Lámparas LED R8'
];

$servicio_nombre = $nombres_servicios[$servicio] ?? 'Servicio';

// Consultar a la BD si el servicio requiere presupuesto
$requiere_presupuesto = 0;
$duracion = '';

try {
    $stmt = $link->prepare("SELECT requiere_presupuesto, duracion FROM servicio WHERE nombre = ?");
    $stmt->bind_param("s", $servicio);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $requiere_presupuesto = $row['requiere_presupuesto'];
        $duracion = $row['duracion'];
    }
    $stmt->close();
} catch (Exception $e) {
    // Si hay error en la consulta, usar lógica por defecto
    error_log("Error consultando servicio: " . $e->getMessage());
}

// Servicios que son productos (no servicios de auto)
$servicios_productos = ['lampara-led-ir', 'lampara-led-r8', 'alfombras-ziel'];

// Servicios para el hogar
$servicios_hogar = ['limpieza-colchones', 'limpieza-sillones'];

// Servicios de auto (excluyendo productos y hogar)
$servicios_auto = array_diff(array_keys($nombres_servicios), $servicios_productos, $servicios_hogar);

// Construir mensaje personalizado según el tipo de servicio y requiere_presupuesto
if (in_array($servicio, $servicios_productos)) {
    // Mensaje para PRODUCTOS (lámparas, alfombras)
    $mensaje = "Hola! 👋\n";
    $mensaje .= "Me interesa el producto: *{$servicio_nombre}*\n\n";
    $mensaje .= "Por favor, necesito información sobre:\n";
    $mensaje .= "💰 Precio del producto\n";
    $mensaje .= "📦 Disponibilidad y stock\n";
    $mensaje .= "🚚 Opciones de envío o retiro\n";
    $mensaje .= "💳 Métodos de pago aceptados\n\n";
    $mensaje .= "¡Gracias! 🛒";
    
} elseif (in_array($servicio, $servicios_hogar)) {
    // Mensaje para SERVICIOS DEL HOGAR (colchones, sillones)
    $mensaje = "Hola! 👋\n";
    $mensaje .= "Me interesa el servicio: *{$servicio_nombre}*\n\n";
    $mensaje .= "Por favor, necesito:\n";
    $mensaje .= "💰 Presupuesto personalizado\n";
    $mensaje .= "📅 Disponibilidad para el servicio\n";
    $mensaje .= "⏰ Tiempo estimado de trabajo\n";
    $mensaje .= "📍 Si realizan el servicio a domicilio\n\n";
    $mensaje .= "¡Gracias! 🏠";
    
} elseif (in_array($servicio, $servicios_auto)) {
    // Servicios de AUTO - usar información de la BD
    if ($requiere_presupuesto == 1) {
        // Servicios que REQUIEREN PRESUPUESTO según BD
        $mensaje = "Hola! 👋\n";
        $mensaje .= "Me gustaría solicitar un *presupuesto personalizado* para:\n\n";
        $mensaje .= "📋 *Servicio:* {$servicio_nombre}\n";
        $mensaje .= "🚗 *Tipo de vehículo:* {$tipo_vehiculo}\n";
        
        if (!empty($duracion)) {
            $mensaje .= "⏱️ *Duración estimada:* {$duracion}\n";
        }
        
        $mensaje .= "\nPor favor, necesito:\n";
        $mensaje .= "💰 Cotización personalizada\n";
        $mensaje .= "📅 Disponibilidad de turnos\n";
        $mensaje .= "🛠️ Evaluación del vehículo\n";
        $mensaje .= "📝 Detalles específicos del trabajo\n\n";
        $mensaje .= "¡Gracias! 🚗";
        
    } else {
        // Servicios con PRECIO FIJO según BD
        $mensaje = "Hola! 👋\n";
        $mensaje .= "Me gustaría consultar sobre el servicio: *{$servicio_nombre}*\n\n";
        
        if ($precio > 0) {
            $mensaje .= "Precio de referencia: *$" . number_format($precio, 0, ',', '.') . "*\n";
        }
        
        if (!empty($duracion)) {
            $mensaje .= "⏱️ Duración estimada: *{$duracion}*\n";
        }
        
        $mensaje .= "\nPor favor, necesito:\n";
        $mensaje .= "📅 Información sobre disponibilidad\n";
        
        if ($precio > 0) {
            $mensaje .= "💰 Confirmación de precio exacto\n";
        } else {
            $mensaje .= "💰 Información de precios actualizada\n";
        }
        
        $mensaje .= "⏰ Horarios disponibles\n";
        
        if ($servicio === 'limpieza-tapizados') {
            $mensaje .= "🪑 Opciones para diferentes tipos de vehículos\n";
        }
        
        $mensaje .= "\n¡Muchas gracias! 🚗";
    }
    
} else {
    // Servicio no reconocido - mensaje genérico
    $mensaje = "Hola! 👋\n";
    $mensaje .= "Me gustaría obtener información sobre: *{$servicio_nombre}*\n\n";
    $mensaje .= "Por favor, necesito:\n";
    $mensaje .= "💰 Información de precios\n";
    $mensaje .= "📅 Disponibilidad\n";
    $mensaje .= "⏰ Tiempos de realización\n";
    $mensaje .= "📝 Detalles del servicio\n\n";
    $mensaje .= "¡Gracias! 🚗";
}

$numero_whatsapp = "2291416897";
$mensaje_codificado = urlencode($mensaje);
$url_whatsapp = "https://wa.me/{$numero_whatsapp}?text={$mensaje_codificado}";

header("Location: $url_whatsapp");
exit();
?>