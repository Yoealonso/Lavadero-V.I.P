<?php
session_start();

$servicios_validos = [
    'pre-venta-basic',
    'pre-venta-premium',
    'lavado-premium-auto',
    'lavado-premium-camioneta',
    'lavado-premium-suv',
    'lavado-vip-extreme',
    'tratamiento-ceramico',
    'abrillantado-carroceria',
    'limpieza-motor',
    'pulido-opticas',
    'pintura-llantas',
    'limpieza-tapizados',
    'alfombras-ziel',
    'colchones',
    'sillones',
    'lampara-led-ir',
    'lampara-led-r8'
];

$servicio = $_GET['servicio'] ?? 'pre-venta-basic';

if (!in_array($servicio, $servicios_validos)) {
    $servicio = 'pre-venta-basic';
}

$_SESSION['servicio_redirigido'] = $servicio;

header("Location: reserva.php?servicio=" . urlencode($servicio));
exit;
?>