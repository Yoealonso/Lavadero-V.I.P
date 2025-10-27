<?php
class DBPrecioService implements PrecioServiceInterface {
    private $db;
    
    public function __construct(mysqli $db) {
        $this->db = $db;
    }
    
    public function calcularPrecio(string $servicio, string $tipoVehiculo): float {
        $stmt = $this->db->prepare(
            "SELECT p.precio FROM precio_servicio p 
            JOIN servicio s ON p.idservicio = s.idservicio 
            WHERE s.nombre = ? AND p.tipo_vehiculo = ?"
        );
        $stmt->bind_param("ss", $servicio, $tipoVehiculo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ? floatval($row['precio']) : $this->getPrecioPorDefecto($servicio, $tipoVehiculo);
    }
    
    private function getPrecioPorDefecto(string $servicio, string $tipoVehiculo): float {
        $precios = [
            'basico' => ['auto' => 1500, 'camioneta' => 1800, 'suv' => 2000],
            'premium' => ['auto' => 2500, 'camioneta' => 3000, 'suv' => 3500],
            'full' => ['auto' => 3500, 'camioneta' => 4000, 'suv' => 4500]
        ];
        return $precios[$servicio][$tipoVehiculo] ?? $precios['basico'][$tipoVehiculo];
    }
}
?>