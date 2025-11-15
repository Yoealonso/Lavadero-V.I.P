<?php
interface NotificacionServiceInterface {
    public function enviarConfirmacionCliente(array $cliente, array $turno): bool;
    public function enviarNotificacionLavadero(array $turno): bool;
    public function enviarConfirmacionLavadero(array $turno): bool;
}
?>