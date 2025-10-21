<?php
class WhatsAppNotificacionService implements NotificacionServiceInterface {
    private $apiUrl;
    private $token;
    private $numeroLavadero;
    
    public function __construct(string $apiUrl, string $token, string $numeroLavadero = '2291416897') {
        $this->apiUrl = $apiUrl;
        $this->token = $token;
        $this->numeroLavadero = $numeroLavadero;
    }
    
    public function enviarConfirmacionCliente(array $cliente, array $turno): bool {
        $mensaje = $this->construirMensajeConfirmacionCliente($cliente, $turno);
        return $this->enviarMensaje($cliente['telefono'], $mensaje);
    }
    
    public function enviarNotificacionLavadero(array $turno): bool {
        $mensaje = $this->construirMensajeNotificacionLavadero($turno);
        return $this->enviarMensaje($this->numeroLavadero, $mensaje);
    }
    
    public function enviarConfirmacionLavadero(array $turno): bool {
        $mensaje = $this->construirMensajeConfirmacionLavadero($turno);
        return $this->enviarMensaje($this->numeroLavadero, $mensaje);
    }
    
    private function construirMensajeConfirmacionCliente(array $cliente, array $turno): string {
        return "¡Hola {$cliente['nombre']}! 📅\n\n" .
               "Tu turno en VIP CAR WASH está pendiente de confirmación:\n" .
               "📋 Servicio: {$turno['servicio']}\n" .
               "🚗 Vehículo: {$turno['vehiculo']}\n" .
               "📅 Fecha: {$turno['fecha']}\n" .
               "⏰ Hora: {$turno['hora']}\n" .
               "💲 Precio: $" . number_format($turno['precio'], 0, ',', '.') . "\n\n" .
               "Para CONFIRMAR tu turno, haz clic aquí:\n" .
               "{$turno['url_confirmacion']}\n\n" .
               "Si no puedes asistir, por favor ignora este mensaje.\n" .
               "¡Gracias! 🚗💨";
    }
    
    private function construirMensajeNotificacionLavadero(array $turno): string {
        return "🆕 NUEVO TURNO SOLICITADO\n\n" .
               "Cliente: {$turno['cliente']}\n" .
               "Teléfono: {$turno['cliente_telefono']}\n" .
               "Servicio: {$turno['servicio_nombre']}\n" .
               "Vehículo: {$turno['vehiculo']} ({$turno['patente']})\n" .
               "Fecha: {$turno['fecha_formateada']}\n" .
               "Hora: {$turno['hora']}\n" .
               "Precio: $" . number_format($turno['precio_final'], 0, ',', '.') . "\n" .
               "Estado: Pendiente de confirmación\n\n" .
               "Esperando confirmación del cliente...";
    }
    
    private function construirMensajeConfirmacionLavadero(array $turno): string {
        return "✅ TURNO CONFIRMADO\n\n" .
               "Cliente: {$turno['cliente']}\n" .
               "Teléfono: {$turno['telefono']}\n" .
               "Servicio: {$turno['servicio']}\n" .
               "Vehículo: {$turno['vehiculo']} ({$turno['patente']})\n" .
               "Fecha: {$turno['fecha']}\n" .
               "Hora: {$turno['hora']}\n" .
               "Precio: $" . number_format($turno['precio'], 0, ',', '.') . "\n\n" .
               "¡Todo listo! 🚗✨";
    }
    
    private function enviarMensaje(string $numero, string $mensaje): bool {
        // SIMULACIÓN - En producción usar API real de WhatsApp
        $numeroFormateado = $this->formatearNumero($numero);
        $mensajeCodificado = urlencode($mensaje);
        $urlWhatsApp = "https://wa.me/{$numeroFormateado}?text={$mensajeCodificado}";
        
        error_log("WHATSAPP SIMULADO: " . $urlWhatsApp);
        
        // En producción, aquí iría la llamada real a la API
        // return $this->llamarAPIWhatsApp($numero, $mensaje);
        
        return true;
    }
    
    private function formatearNumero(string $numero): string {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        if (strlen($numero) === 10) {
            $numero = '54' . $numero;
        }
        return $numero;
    }
}
?>