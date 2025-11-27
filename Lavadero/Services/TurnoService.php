<?php
class TurnoService {
    private $turnoRepository;
    private $notificacionService;
    private $precioService;
    
    public function __construct($turnoRepository, $notificacionService, $precioService) {
        $this->turnoRepository = $turnoRepository;
        $this->notificacionService = $notificacionService;
        $this->precioService = $precioService;
    }
    
    public function procesarSolicitudTurno($postData) {
        $datosValidados = $this->validarDatos($postData);
        
        if (!$this->turnoRepository->verificarDisponibilidad($datosValidados["fecha"])) {
            throw new Exception("No hay turnos disponibles para la fecha seleccionada. Máximo 3 turnos por día.");
        }
        
        $plazas = ($datosValidados["servicio"] === "limpieza-tapizados") ? $datosValidados["plazas"] : 4;
        $precio = $this->precioService->calcularPrecio($datosValidados["servicio"], $datosValidados["tipo_vehiculo"], $plazas);
        $token = bin2hex(random_bytes(16));
        
        $idCliente = $this->turnoRepository->guardarCliente($datosValidados);
        $idVehiculo = $this->turnoRepository->guardarVehiculo($datosValidados, $idCliente);
        $idServicio = $this->turnoRepository->obtenerServicioId($datosValidados["servicio"]);
        $idTurno = $this->turnoRepository->guardarTurno($datosValidados, $idCliente, $idVehiculo, $idServicio, $precio, $token);
        
        $numeroTurno = $this->turnoRepository->asignarNumeroTurno($datosValidados["fecha"]) - 1;
        $turnoData = $this->construirDatosTurno($datosValidados, $idTurno, $precio, $token, $plazas, $numeroTurno);
        
        $this->notificacionService->enviarNotificacionLavadero($turnoData);
        $this->notificacionService->enviarConfirmacionCliente(
            ["nombre" => $datosValidados["nombre"], "telefono" => $datosValidados["telefono"]],
            $turnoData
        );
        
        return $turnoData;
    }
    
    private function validarDatos($data) {
        $camposRequeridos = ["name", "lastName", "phone", "email", "brand", "type", "model", "patent", "color", "date", "datetime", "servicio"];
        
        foreach ($camposRequeridos as $campo) {
            if (empty($data[$campo])) {
                throw new Exception("El campo $campo es requerido");
            }
        }
        
        return [
            "nombre" => trim($data["name"]),
            "apellido" => trim($data["lastName"]),
            "telefono" => trim($data["phone"]),
            "email" => trim($data["email"]),
            "direccion" => $data["location"] ?? "",
            "marca" => trim($data["brand"]),
            "modelo" => trim($data["model"]),
            "tipo_vehiculo" => trim($data["type"]),
            "patente" => trim($data["patent"]),
            "color" => trim($data["color"]),
            "anio" => $data["releaseDate"] ?? null,
            "detalles" => $data["details"] ?? "",
            "fecha" => trim($data["date"]),
            "hora" => trim($data["datetime"]),
            "servicio" => trim($data["servicio"]),
            "plazas" => $data["plazas"] ?? 4
        ];
    }
    
    private function construirDatosTurno($datos, $idTurno, $precio, $token, $plazas = 4, $numeroTurno = 1) {
        $baseUrl = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]);
        
        $turnoData = [
            "id" => $idTurno,
            "cliente" => $datos["nombre"] . " " . $datos["apellido"],
            "cliente_nombre" => $datos["nombre"],
            "cliente_telefono" => $datos["telefono"],
            "vehiculo" => $datos["marca"] . " " . $datos["modelo"],
            "patente" => $datos["patente"],
            "fecha_formateada" => date("d/m/Y", strtotime($datos["fecha"])),
            "hora" => "A confirmar",
            "servicio_nombre" => $this->obtenerNombreServicio($datos["servicio"]),
            "precio_final" => $precio,
            "url_confirmacion" => $baseUrl . "/confirmar_turno.php?token=" . $token
        ];
        
        return $turnoData;
    }
    
    private function obtenerNombreServicio($servicio) {
        $servicios = [
            "pre-venta-basic" => "Pre Venta Basic",
            "pre-venta-premium" => "Pre Venta Premium",
            "lavado-premium-auto" => "Lavado Premium Auto",
            "lavado-premium-camioneta" => "Lavado Premium Camioneta",
            "lavado-premium-suv" => "Lavado Premium SUV",
            "lavado-vip-extreme" => "Lavado VIP Extreme",
            "tratamiento-ceramico" => "Tratamiento Cerámico",
            "abrillantado-carroceria" => "Abrillantado de Carrocería",
            "limpieza-motor" => "Limpieza y Acondicionado de Motor",
            "pulido-opticas" => "Pulido y Sellado de Ópticas",
            "pintura-llantas" => "Pintura de Llantas",
            "limpieza-tapizados" => "Limpieza de Tapizados"
        ];
        
        return $servicios[$servicio] ?? "Servicio Personalizado";
    }
}
?>