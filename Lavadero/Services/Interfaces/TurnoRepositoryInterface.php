<?php
interface TurnoRepositoryInterface {
    public function guardarCliente(array $datos): int;
    public function guardarVehiculo(array $datos, int $idCliente): int;
    public function guardarTurno(array $datos, int $idCliente, int $idVehiculo, int $idServicio, float $precio, string $token): int;
    public function obtenerServicioId(string $servicio): int;
    public function verificarDisponibilidad(string $fecha): bool;
    public function confirmarTurno(string $token): bool;
    public function obtenerTurnoPorToken(string $token): array;
    public function asignarNumeroTurno(string $fecha): int;
}
?>