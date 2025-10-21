<?php
class TurnoService {
    private $turnoRepository;
    private $notificacionService;
    private $precioService;
    
    public function __construct(
        TurnoRepositoryInterface $turnoRepository,
        NotificacionServiceInterface $notificacionService,
        PrecioServiceInterface $precioService
    ) {
        $this->turnoRepository = $turnoRepository;
        $this->notificacionService = $notificacionService;
        $this->precioService = $precioService;
    }
    
    public function procesarSolicitudTurno(array $postData): array {
        $validador = new ValidadorTurno();
        $datosValidados = $validador->validar($postData);
        
        if (!$this->turnoRepository->verificarDisponibilidad($datosValidados['fecha'])) {
            throw new Exception("No hay turnos disponibles para la fecha seleccionada. Máximo 3 turnos por día.");
        }
        
        $precio = $this->precioService->calcularPrecio($datosValidados['servicio'], $datosValidados['tipo_vehiculo']);
        $token = bin2hex(random_bytes(16));
        
        $idCliente = $this->turnoRepository->guardarCliente($datosValidados);
        $idVehiculo = $this->turnoRepository->guardarVehiculo($datosValidados, $idCliente);
        $idServicio = $this->turnoRepository->obtenerServicioId($datosValidados['servicio']);
        $idTurno = $this->turnoRepository->guardarTurno($datosValidados, $idCliente, $idVehiculo, $idServicio, $precio, $token);
        
        $turnoData = $this->construirDatosTurno($datosValidados, $idTurno, $precio, $token);
        
        // Enviar notificaciones
        $this->notificacionService->enviarNotificacionLavadero($turnoData);
        $this->notificacionService->enviarConfirmacionCliente(
            ['nombre' => $datosValidados['nombre'], 'telefono' => $datosValidados['telefono']],
            $turnoData
        );
        
        return $turnoData;
    }
    
    private function construirDatosTurno(array $datos, int $idTurno, float $precio, string $token): array {
        $baseUrl = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
        
        return [
            'id' => $idTurno,
            'cliente' => $datos['nombre'] . ' ' . $datos['apellido'],
            'cliente_nombre' => $datos['nombre'],
            'cliente_telefono' => $datos['telefono'],
            'vehiculo' => $datos['marca'] . ' ' . $datos['modelo'],
            'vehiculo_marca' => $datos['marca'],
            'vehiculo_modelo' => $datos['modelo'],
            'patente' => $datos['patente'],
            'fecha' => $datos['fecha'],
            'fecha_formateada' => date('d/m/Y', strtotime($datos['fecha'])),
            'hora' => $datos['hora'],
            'servicio' => $datos['servicio'],
            'servicio_nombre' => $this->obtenerNombreServicio($datos['servicio']),
            'precio_final' => $precio,
            'url_confirmacion' => $baseUrl . "/confirmar_turno.php?token=" . $token
        ];
    }
    
    private function obtenerNombreServicio(string $servicio): string {
        $nombres = [
            'basico' => 'Lavado Básico',
            'premium' => 'Lavado Premium', 
            'full' => 'Lavado Full'
        ];
        return $nombres[$servicio] ?? 'Servicio';
    }
}

class ValidadorTurno {
    public function validar(array $data): array {
        $this->validarCamposRequeridos($data);
        
        return [
            'nombre' => $this->sanitizarTexto($data['name']),
            'apellido' => $this->sanitizarTexto($data['lastName']),
            'telefono' => $this->sanitizarTelefono($data['phone']),
            'email' => $this->sanitizarEmail($data['email']),
            'direccion' => $this->sanitizarTexto($data['location'] ?? ''),
            'marca' => $this->sanitizarTexto($data['brand'] ?? ''),
            'modelo' => $this->sanitizarTexto($data['model'] ?? ''),
            'tipo_vehiculo' => $this->sanitizarTexto($data['type'] ?? 'auto'),
            'anio' => intval($data['releaseDate'] ?? 0),
            'patente' => strtoupper($this->sanitizarTexto($data['patent'] ?? '')),
            'color' => $this->sanitizarTexto($data['color'] ?? ''),
            'detalles' => $this->sanitizarTexto($data['details'] ?? ''),
            'fecha' => $this->validarFecha($data['date']),
            'hora' => $data['datetime'],
            'servicio' => $this->sanitizarTexto($data['servicio'] ?? 'basico')
        ];
    }
    
    private function validarCamposRequeridos(array $data): void {
        $required = ['name', 'lastName', 'phone', 'email', 'date', 'datetime'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("El campo " . $field . " es requerido.");
            }
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El email no es válido.");
        }
        
        if (strtotime($data['date']) <= strtotime('today')) {
            throw new Exception("La fecha debe ser futura.");
        }
    }
    
    private function sanitizarTexto(string $text): string {
        return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
    }
    
    private function sanitizarTelefono(string $telefono): string {
        $telefono = preg_replace('/[^0-9+\-\s]/', '', $telefono);
        return $this->sanitizarTexto($telefono);
    }
    
    private function sanitizarEmail(string $email): string {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    private function validarFecha(string $fecha): string {
        if (strtotime($fecha) <= strtotime('today')) {
            throw new Exception("La fecha debe ser futura.");
        }
        return $fecha;
    }
}
?>