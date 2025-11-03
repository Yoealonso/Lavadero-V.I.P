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
        $stmt = $this->db->prepare(
            "INSERT INTO turno (fechaReserva, horaReserva, estado, precio_final, token_confirmacion, idcliente, idvehiculo, idservicio) 
             VALUES (?, ?, 'pendiente', ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssdsiii", 
            $datos['fecha'], 
            $datos['hora'], 
            $precio, 
            $token, 
            $idCliente, 
            $idVehiculo, 
            $idServicio
        );
        $stmt->execute();
        $idTurno = $stmt->insert_id;
        $stmt->close();
        
        return $idTurno;
    }
    
    public function obtenerServicioId(string $servicio): int {
        $stmt = $this->db->prepare("SELECT idservicio FROM servicio WHERE nombre = ?");
        $stmt->bind_param("s", $servicio);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ? intval($row['idservicio']) : 1; // Default al servicio básico
    }
    
    public function verificarDisponibilidad(string $fecha): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM turno 
             WHERE fechaReserva = ? AND estado IN ('pendiente', 'confirmado')"
        );
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return ($row['total'] ?? 0) < 3; // Máximo 3 turnos por día
    }
    
    public function confirmarTurno(string $token): bool {
        $stmt = $this->db->prepare(
            "UPDATE turno SET estado = 'confirmado', fecha_confirmacion = NOW() 
             WHERE token_confirmacion = ? AND estado = 'pendiente'"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $affected = $stmt->affected_rows > 0;
        $stmt->close();
        
        return $affected;
    }
    
    public function obtenerTurnoPorToken(string $token): array {
        $stmt = $this->db->prepare(
            "SELECT t.*, c.nombre, c.apellido, c.telefono, c.email,
                    v.marca, v.modelo, v.patente, v.color,
                    s.nombre as servicio_nombre
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
}
?>