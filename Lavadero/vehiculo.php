<?php
session_start();

$servicio = $_GET['servicio'] ?? '';

// Mapear los servicios simplificados del frontend a los servicios reales
$servicios_mapeo = [
    'basico' => 'pre-venta-basic',
    'premium' => 'pre-venta-premium', 
    'full' => 'lavado-premium-auto', 
    'tapizados' => 'limpieza-tapizados'
];

$servicio_real = $servicios_mapeo[$servicio] ?? 'pre-venta-basic';

// Guardar en sesión para usar en el formulario de reserva
$_SESSION['servicio_redirigido'] = $servicio_real;

// Redirigir a reserva.php con el servicio correcto
header("Location: reserva.php?servicio=" . urlencode($servicio_real));
exit;
?>