<?php
class DBPrecioService implements PrecioServiceInterface {
    private $db;
    
    public function __construct(mysqli $db) {
        $this->db = $db;
    }
    
    public function calcularPrecio(string $servicio, string $tipoVehiculo, int $plazas = 4): float {
        // Mapear nombres de servicio de frontend a BD
        $servicioBD = $this->mapearServicioABD($servicio);
        
        if ($servicioBD === 'limpieza-tapizados') {
            // Para limpieza de tapizados, usar las plazas
            $stmt = $this->db->prepare(
                "SELECT p.precio FROM precio_servicio p 
                JOIN servicio s ON p.idservicio = s.idservicio 
                WHERE s.nombre = ? AND p.tipo_vehiculo = ? AND p.plazas = ?"
            );
            $stmt->bind_param("ssi", $servicioBD, $tipoVehiculo, $plazas);
        } else {
            // Para otros servicios, buscar por tipo de vehículo (plazas=4 por defecto)
            $stmt = $this->db->prepare(
                "SELECT p.precio FROM precio_servicio p 
                JOIN servicio s ON p.idservicio = s.idservicio 
                WHERE s.nombre = ? AND p.tipo_vehiculo = ?"
            );
            $stmt->bind_param("ss", $servicioBD, $tipoVehiculo);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row) {
            return floatval($row['precio']);
        }
        
        // Fallback a precios por defecto si no encuentra en BD
        return $this->getPrecioPorDefecto($servicioBD, $tipoVehiculo, $plazas);
    }
    
    private function mapearServicioABD(string $servicioFrontend): string {
        $mapeo = [
            'basico' => 'pre-venta-basic',
            'premium' => 'pre-venta-premium',
            'full' => 'lavado-premium-auto',
            'tapizados' => 'limpieza-tapizados'
        ];
        
        return $mapeo[$servicioFrontend] ?? $servicioFrontend;
    }
    
    private function getPrecioPorDefecto(string $servicio, string $tipoVehiculo, int $plazas = 4): float {
        $precios = [
            'pre-venta-basic' => ['auto' => 200000, 'camioneta' => 200000, 'suv' => 200000],
            'pre-venta-premium' => ['auto' => 300000, 'camioneta' => 300000, 'suv' => 300000],
            'lavado-premium-auto' => ['auto' => 40000, 'camioneta' => 40000, 'suv' => 40000],
            'lavado-premium-camioneta' => ['auto' => 50000, 'camioneta' => 50000, 'suv' => 50000],
            'lavado-premium-suv' => ['auto' => 45000, 'camioneta' => 45000, 'suv' => 45000],
            'lavado-vip-extreme' => ['auto' => 0, 'camioneta' => 0, 'suv' => 0], // requiere_presupuesto
            'tratamiento-ceramico' => ['auto' => 0, 'camioneta' => 0, 'suv' => 0], // requiere_presupuesto
            'abrillantado-carroceria' => ['auto' => 0, 'camioneta' => 0, 'suv' => 0], // requiere_presupuesto
            'limpieza-motor' => ['auto' => 40000, 'camioneta' => 40000, 'suv' => 40000],
            'pulido-opticas' => ['auto' => 60000, 'camioneta' => 60000, 'suv' => 60000],
            'pintura-llantas' => ['auto' => 0, 'camioneta' => 0, 'suv' => 0], // requiere_presupuesto
            'limpieza-tapizados' => [
                'auto' => [4 => 150000, 7 => 200000],
                'camioneta' => [4 => 150000, 7 => 200000],
                'suv' => [4 => 150000, 7 => 200000]
            ]
        ];
        
        if ($servicio === 'limpieza-tapizados') {
            return $precios[$servicio][$tipoVehiculo][$plazas] ?? $precios[$servicio][$tipoVehiculo][4];
        }
        
        return $precios[$servicio][$tipoVehiculo] ?? 0;
    }
}
?>