<?php
class DBPrecioService implements PrecioServiceInterface {
    private $db;
    
    public function __construct(mysqli $db) {
        $this->db = $db;
    }
    
    public function calcularPrecio(string $servicio, string $tipoVehiculo, int $plazas = 4): float {
        if ($servicio === 'limpieza-tapizados') {
            // Para limpieza de tapizados, considerar las plazas
            $stmt = $this->db->prepare(
                "SELECT p.precio FROM precio_servicio p 
                JOIN servicio s ON p.idservicio = s.idservicio 
                WHERE s.nombre = ? AND p.tipo_vehiculo = ? AND p.plazas = ?"
            );
            $stmt->bind_param("ssi", $servicio, $tipoVehiculo, $plazas);
        } else {
            // Para otros servicios, usar plazas por defecto (4)
            $stmt = $this->db->prepare(
                "SELECT p.precio FROM precio_servicio p 
                JOIN servicio s ON p.idservicio = s.idservicio 
                WHERE s.nombre = ? AND p.tipo_vehiculo = ? AND p.plazas = 4"
            );
            $stmt->bind_param("ss", $servicio, $tipoVehiculo);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ? floatval($row['precio']) : $this->getPrecioPorDefecto($servicio, $tipoVehiculo, $plazas);
    }
    
    private function getPrecioPorDefecto(string $servicio, string $tipoVehiculo, int $plazas = 4): float {
        $precios = [
            'pre-venta-basic' => ['auto' => 8000, 'camioneta' => 9500, 'suv' => 11000],
            'pre-venta-premium' => ['auto' => 15000, 'camioneta' => 18000, 'suv' => 21000],
            'lavado-premium-auto' => ['auto' => 3500, 'camioneta' => 4200, 'suv' => 4800],
            'lavado-premium-camioneta' => ['auto' => 3800, 'camioneta' => 4500, 'suv' => 5200],
            'lavado-premium-suv' => ['auto' => 4000, 'camioneta' => 4800, 'suv' => 5500],
            'lavado-vip-extreme' => ['auto' => 12000, 'camioneta' => 14500, 'suv' => 17000],
            'tratamiento-ceramico' => ['auto' => 20000, 'camioneta' => 24000, 'suv' => 28000],
            'abrillantado-carroceria' => ['auto' => 9000, 'camioneta' => 11000, 'suv' => 13000],
            'limpieza-motor' => ['auto' => 4500, 'camioneta' => 5500, 'suv' => 6500],
            'pulido-opticas' => ['auto' => 3000, 'camioneta' => 3500, 'suv' => 4000],
            'pintura-llantas' => ['auto' => 7000, 'camioneta' => 8500, 'suv' => 10000],
            'limpieza-tapizados' => [
                'auto' => [4 => 6000, 7 => 7500],
                'camioneta' => [4 => 7500, 7 => 9000],
                'suv' => [4 => 9000, 7 => 11000]
            ]
        ];
        
        if ($servicio === 'limpieza-tapizados') {
            return $precios[$servicio][$tipoVehiculo][$plazas] ?? $precios[$servicio][$tipoVehiculo][4];
        }
        
        return $precios[$servicio][$tipoVehiculo] ?? $precios['pre-venta-basic'][$tipoVehiculo];
    }
}
?>