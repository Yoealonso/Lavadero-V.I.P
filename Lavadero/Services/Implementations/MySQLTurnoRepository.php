<?php
class MySQLTurnoRepository implements TurnoRepositoryInterface {
    private $db;
    
    public function __construct(mysqli $db) {
        $this->db = $db;
    }
    
    public function guardarCliente(array $datos): int {
        $stmt = $this->db->prepare(
            "INSERT INTO cliente (nombre, apellido, telefono, email, direccion) 
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssss", 
            $datos['nombre'], 
            $datos['apellido'], 
            $datos['telefono'], 
            $datos['email'], 
            $datos['direccion']
        );
        $stmt->execute();
        $idCliente = $stmt->insert_id;
        $stmt->close();
        
        return $idCliente;
    }
    
    public function guardarVehiculo(array $datos, int $idCliente): int {
        $stmt = $this->db->prepare(
            "INSERT INTO vehiculo (tipo, marca, modelo, anio, patente, color, detalles, idcliente) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssisssi", 
            $datos['tipo_vehiculo'], 
            $datos['marca'], 
            $datos['modelo'], 
            $datos['anio'], 
            $datos['patente'], 
            $datos['color'], 
            $datos['detalles'], 
            $idCliente
        );
        $stmt->execute();
        $idVehiculo = $stmt->insert_id;
        $stmt->close();
        
        return $idVehiculo;
    }
    
    public function guardarTurno(array $datos, int $idCliente, int $idVehiculo, int $idServicio, float $precio, string $token): int {
        // Asignar número de turno automáticamente
        $numeroTurno = $this->asignarNumeroTurno($datos['fecha']);
        
        $stmt = $this->db->prepare(
            "INSERT INTO turno (fechaReserva, horaReserva, idcliente, idvehiculo, idservicio, estado, precio_final, token_confirmacion, numero_turno) 
            VALUES (?, '00:00:00', ?, ?, ?, 'pendiente', ?, ?, ?)"
        );
        
        $stmt->bind_param(
            "siiisdi", 
            $datos['fecha'], 
            $idCliente,
            $idVehiculo,
            $idServicio,
            $precio,
            $token,
            $numeroTurno
        );
        
        $stmt->execute();
        $idTurno = $stmt->insert_id;
        $stmt->close();
        
        return $idTurno;
    }
    
    public function obtenerServicioId(string $servicio): int {
        $servicioBD = $this->mapearServicioABD($servicio);
        
        echo "<!-- DEBUG: Buscando servicio: '$servicio' -> '$servicioBD' -->";
        
        $stmt = $this->db->prepare("SELECT idservicio FROM servicio WHERE nombre = ?");
        $stmt->bind_param("s", $servicioBD);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if (!$row) {
            // Si no encuentra el servicio, buscar por LIKE o lanzar excepción
            error_log("❌ SERVICIO NO ENCONTRADO: " . $servicioBD);
            
            // Buscar alternativas
            $stmt2 = $this->db->prepare("SELECT idservicio FROM servicio WHERE nombre LIKE ?");
            $likeServicio = "%" . $servicioBD . "%";
            $stmt2->bind_param("s", $likeServicio);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $row2 = $result2->fetch_assoc();
            $stmt2->close();
            
            if ($row2) {
                return intval($row2['idservicio']);
            }
            
            throw new Exception("Servicio no encontrado: " . $servicio);
        }
        
        return intval($row['idservicio']);
    }
    
    public function verificarDisponibilidad(string $fecha): bool {
        $numeroTurno = $this->asignarNumeroTurno($fecha);
        return $numeroTurno <= 3;
    }
    
    public function asignarNumeroTurno(string $fecha): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM turno 
            WHERE fechaReserva = ? AND estado IN ('pendiente', 'confirmado')"
        );
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return ($row ? intval($row['count']) : 0) + 1;
    }
    
    public function confirmarTurno(string $token): bool {
        $stmt = $this->db->prepare(
            "UPDATE turno SET estado = 'confirmado', fecha_confirmacion = NOW() 
            WHERE token_confirmacion = ? AND estado = 'pendiente'"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $filasAfectadas = $stmt->affected_rows;
        $stmt->close();
        
        return $filasAfectadas > 0;
    }
    
    public function obtenerTurnoPorToken(string $token): array {
        $stmt = $this->db->prepare(
            "SELECT t.*, c.nombre, c.apellido, c.telefono, c.email,
                    v.marca, v.modelo, v.patente, v.color,
                    s.nombre as servicio_nombre, s.descripcion as servicio_desc
            FROM turno t
            JOIN cliente c ON t.idcliente = c.idcliente
            JOIN vehiculo v ON t.idvehiculo = v.idvehiculo
            JOIN servicio s ON t.idservicio = s.idservicio
            WHERE t.token_confirmacion = ?"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $turno = $result->fetch_assoc();
        $stmt->close();
        
        return $turno ?: [];
    }
    
    private function mapearServicioABD(string $servicioFrontend): string {
    $serviciosBD = [
        'pre-venta-basic', 'pre-venta-premium', 'lavado-premium-auto',
        'lavado-premium-camioneta', 'lavado-premium-suv', 'lavado-vip-extreme',
        'tratamiento-ceramico', 'abrillantado-carroceria', 'limpieza-motor',
        'pulido-opticas', 'pintura-llantas', 'limpieza-tapizados',
        'alfombras-ziel', 'colchones', 'sillones', 'lampara-led-ir', 'lampara-led-r8'
    ];
    
    if (in_array($servicioFrontend, $serviciosBD)) {
        return $servicioFrontend;
    }
    
    $mapeoAntiguo = [
        'basico' => 'pre-venta-basic',
        'premium' => 'pre-venta-premium', 
        'full' => 'lavado-premium-auto',
        'tapizados' => 'limpieza-tapizados'
    ];
    
    return $mapeoAntiguo[$servicioFrontend] ?? 'pre-venta-basic';
}
}
?>