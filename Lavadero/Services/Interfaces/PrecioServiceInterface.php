<?php
interface PrecioServiceInterface {
    public function calcularPrecio(string $servicio, string $tipoVehiculo): float;
}
?>