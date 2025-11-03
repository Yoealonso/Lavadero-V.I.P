<?php
session_start();

// Mapeo de servicios
$servicios_mapeo = [
    'basico' => 'pre-venta-basic',
    'premium' => 'pre-venta-premium', 
    'full' => 'lavado-premium-auto',
    'tapizados' => 'limpieza-tapizados'
];

$servicio_solicitado = $_GET['servicio'] ?? 'pre-venta-basic';
$servicio_real = $servicios_mapeo[$servicio_solicitado] ?? $servicio_solicitado;

// Guardar en sesión para usar en reserva.php
$_SESSION['servicio_redirigido'] = $servicio_real;

// Redirigir a la página de reserva
header("Location: reserva.php?servicio=" . urlencode($servicio_real));
exit;
?>