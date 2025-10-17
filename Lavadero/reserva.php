<?php
include_once("C:/xampp/htdocs/Lavadero/Conexion/conexion.php");
error_reporting(E_ALL);

function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

function checkTurnoAvailability($link, $fecha) {
    $stmt = $link->prepare("SELECT COUNT(*) as total FROM turno WHERE fechaReserva = ? AND estado IN ('pendiente', 'confirmado')");
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] < 3;
}

// FUNCIÓN CORREGIDA - Usa tabla existente o alternativa
function calculatePrice($servicio, $tipoVehiculo) {
    $precios = [
        'basico' => ['auto' => 1500, 'camioneta' => 1800, 'suv' => 2000],
        'premium' => ['auto' => 2500, 'camioneta' => 3000, 'suv' => 3500],
        'full' => ['auto' => 3500, 'camioneta' => 4000, 'suv' => 4500]
    ];
    
    return $precios[$servicio][$tipoVehiculo] ?? 0;
}

function validateData($nombre, $fecha, $telefono, $email) {
    $errors = [];
    
    if (empty($nombre) || strlen($nombre) < 2) {
        $errors[] = "Nombre inválido";
    }
    
    if (strtotime($fecha) <= strtotime('today')) {
        $errors[] = "La fecha debe ser futura";
    }
    
    if (!preg_match('/^[0-9]{10,15}$/', $telefono)) {
        $errors[] = "Teléfono inválido";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido";
    }
    
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitizar datos
        $nombre = sanitizeInput($_POST['name']);
        $apellido = sanitizeInput($_POST['lastName']);
        $telefono = sanitizeInput($_POST['phone']);
        $email = sanitizeInput($_POST['email']);
        $direccion = sanitizeInput($_POST['location']);
        $marca = sanitizeInput($_POST['brand']);
        $tipo = sanitizeInput($_POST['type']);
        $modelo = sanitizeInput($_POST['model']);
        $anio = intval($_POST['releaseDate']);
        $patente = strtoupper(sanitizeInput($_POST['patent']));
        $detalles = sanitizeInput($_POST['details']);
        $fecha = sanitizeInput($_POST['date']);
        $hora = sanitizeInput($_POST['datetime']);
        $servicio = sanitizeInput($_POST['servicio']);

        // Validaciones
        $validationErrors = validateData($nombre, $fecha, $telefono, $email);
        if (!empty($validationErrors)) {
            throw new Exception(implode(", ", $validationErrors));
        }

        if (!checkTurnoAvailability($link, $fecha)) {
            throw new Exception("No hay turnos disponibles para $fecha. Máximo 3 turnos por día.");
        }

        // TRANSACCIÓN para asegurar consistencia
        $link->begin_transaction();

        try {
            // Insertar cliente
            $stmtCliente = $link->prepare("INSERT INTO cliente (nombre, apellido, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
            $stmtCliente->bind_param("sssss", $nombre, $apellido, $telefono, $email, $direccion);
            $stmtCliente->execute();
            $idCliente = $link->insert_id;

            // Insertar vehículo
            $stmtVehiculo = $link->prepare("INSERT INTO vehiculo (tipo, marca, modelo, anio, patente, detalles, idcliente) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtVehiculo->bind_param("sssissi", $tipo, $marca, $modelo, $anio, $patente, $detalles, $idCliente);
            $stmtVehiculo->execute();
            $idVehiculo = $link->insert_id;

            // Obtener ID del servicio
            $stmtServicio = $link->prepare("SELECT idservicio FROM servicio WHERE nombre = ?");
            $servicioNombre = $servicio; // 'basico', 'premium', 'full'
            $stmtServicio->bind_param("s", $servicioNombre);
            $stmtServicio->execute();
            $resultServicio = $stmtServicio->get_result();
            $servicioData = $resultServicio->fetch_assoc();
            $idServicio = $servicioData['idservicio'] ?? 1; // Fallback a ID 1

            // Insertar turno
            $stmtTurno = $link->prepare("INSERT INTO turno (fechaReserva, horaReserva, idcliente, idvehiculo, idservicio, estado) VALUES (?, ?, ?, ?, ?, 'pendiente')");
            $stmtTurno->bind_param("ssiii", $fecha, $hora, $idCliente, $idVehiculo, $idServicio);
            $stmtTurno->execute();
            $idTurno = $link->insert_id;

            $link->commit();

            // Calcular precio (sin depender de BD)
            $precio = calculatePrice($servicio, $tipo);
            
            // Preparar datos para la vista
            $reservaData = [
                'id' => $idTurno,
                'cliente' => "$nombre $apellido",
                'vehiculo' => "$marca $modelo",
                'patente' => $patente,
                'fecha' => $fecha,
                'hora' => $hora,
                'servicio' => $servicio,
                'precio' => $precio
            ];
            
            $successMessage = "✅ Turno reservado exitosamente";

        } catch (Exception $e) {
            $link->rollback();
            throw $e;
        }

    } catch (Exception $e) {
        $errorMessage = "❌ Error: " . $e->getMessage();
    }
}

// Siempre incluir la vista
include 'reserva_view.php';
?>