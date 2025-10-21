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
        $stmt->bind_param("sssss", 
            $datos['nombre'], 
            $datos['apellido'], 
            $datos['telefono'], 
            $datos['email'], 
            $datos['direccion']
        );
        $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();
        
        return $id;
    }
    
    public function guardarVehiculo(array $datos, int $idCliente): int {
        $stmt = $this->db->prepare(
            "INSERT INTO vehiculo (tipo, marca, modelo, anio, patente, color, detalles, idcliente) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssissis", 
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
        $id = $this->db->insert_id;
        $stmt->close();
        
        return $id;
    }
    
    public function guardarTurno(array $datos, int $idCliente, int $idVehiculo, int $idServicio, float $precio, string $token): int {
        $stmt = $this->db->prepare(
            "INSERT INTO turno (fechaReserva, horaReserva, estado, precio_final, token_confirmacion, idcliente, idvehiculo, idservicio) 
             VALUES (?, ?, 'pendiente', ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdsiii", 
            $datos['fecha'],
            $datos['hora'],
            $precio,
            $token,
            $idCliente,
            $idVehiculo,
            $idServicio
        );
        $stmt->execute();
        $id = $this->db->insert_id;
        $stmt->close();
        
        return $id;
    }
    
    public function obtenerServicioId(string $servicio): int {
        $stmt = $this->db->prepare("SELECT idservicio FROM servicio WHERE nombre = ?");
        $stmt->bind_param("s", $servicio);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ? $row['idservicio'] : 1;
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
        
        return intval($row['total']) < 3;
    }
    
    public function confirmarTurno(string $token): bool {
        $stmt = $this->db->prepare(
            "UPDATE turno SET estado = 'confirmado', fecha_confirmacion = NOW() 
             WHERE token_confirmacion = ? AND estado = 'pendiente'"
        );
        $stmt->bind_param("s", $token);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    public function obtenerTurnoPorToken(string $token): array {
        $stmt = $this->db->prepare(
            "SELECT t.*, c.nombre, c.apellido, c.telefono, s.nombre as servicio_nombre, 
                    v.marca, v.modelo, v.patente 
             FROM turno t 
             JOIN cliente c ON t.idcliente = c.idcliente 
             JOIN servicio s ON t.idservicio = s.idservicio
             JOIN vehiculo v ON t.idvehiculo = v.idvehiculo
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