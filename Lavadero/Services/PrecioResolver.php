<?php
class PrecioResolver {
    private $precioService;
    
    public function __construct(PrecioServiceInterface $precioService) {
        $this->precioService = $precioService;
    }
    
    public function resolverPrecio(string $servicio, string $tipoVehiculo = null, int $plazas = null): array {
        $resultado = $this->precioService->calcularPrecio($servicio, $tipoVehiculo, $plazas);
        
        return array_merge($resultado, [
            'servicio' => $servicio,
            'nombre_servicio' => $this->obtenerNombreServicio($servicio),
            'tipo_vehiculo' => $tipoVehiculo,
            'plazas' => $plazas
        ]);
    }
    
    public function obtenerBotonReserva(string $servicio, string $tipoVehiculo = null, int $plazas = null): string {
        $precioInfo = $this->precioService->calcularPrecio($servicio, $tipoVehiculo, $plazas);
        
        if ($precioInfo['tipo'] === 'consulta') {
            return '<a href="' . $precioInfo['whatsapp_url'] . '" target="_blank" class="btn btn-consulta">Consultar Presupuesto</a>';
        }
        
        return '<a href="vehiculo.php?servicio=' . $servicio . '" class="btn">Reservar</a>';
    }
    
    public function obtenerPrecioDisplay(string $servicio, string $tipoVehiculo = null, int $plazas = null): string {
        $precioInfo = $this->precioService->calcularPrecio($servicio, $tipoVehiculo, $plazas);
        
        if ($precioInfo['tipo'] === 'consulta') {
            return '<div class="precio-consulta">Consultar presupuesto</div>';
        }
        
        return '<div class="precio">' . $precioInfo['mensaje'] . '</div>';
    }
    
    private function obtenerNombreServicio(string $servicio): string {
        $nombres = [
            'lavado-premium-auto' => 'Lavado Premium Auto',
            'lavado-premium-suv' => 'Lavado Premium SUV',
            'lavado-premium-camioneta' => 'Lavado Premium Camioneta',
            'limpieza-tapizados' => 'Limpieza de Tapizados',
            'limpieza-motor' => 'Limpieza de Motor',
            'pulido-opticas' => 'Pulido y Sellado de Opticas',
            'pre-venta-basic' => 'Servicio Pre Venta Basic',
            'pre-venta-premium' => 'Servicio Pre Venta Premium',
            'abrillantado-carroceria' => 'Abrillantado',
            'tratamiento-ceramico' => 'Tratamiento Cerámico',
            'lavado-colchon-1plaza' => 'Colchón 1 Plaza',
            'lavado-colchon-2plaza-140' => 'Colchón 2 Plazas 1,40m',
            'lavado-colchon-2plaza-160-200' => 'Colchón 2 Plazas 1,60m/2,00m',
            'lavado-sillon-1cuerpo' => 'Sillón 1 Cuerpo',
            'lavado-sillon-2cuerpos' => 'Sillón 2 Cuerpos',
            'lavado-sillon-3cuerpos' => 'Sillón 3 Cuerpos',
            'lavado-alfombras' => 'Alfombras',
            'lavado-lamparas-led' => 'Lámparas LED',
            'lavado-alfombras-termoformadas' => 'Alfombras Termoformadas'
        ];
        
        return $nombres[$servicio] ?? $servicio;
    }
}
?>