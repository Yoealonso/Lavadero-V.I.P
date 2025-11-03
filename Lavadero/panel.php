<?php
// panel.php - Panel de administración PROFESIONAL - VERSIÓN FINAL
include_once("C:/xampp/htdocs/lavadero/Conexion/conexion.php");

// Función para resaltar búsqueda
function resaltarBusqueda($texto, $busqueda) {
    if (empty($busqueda)) return htmlspecialchars($texto);
    $patron = '/(' . preg_quote($busqueda, '/') . ')/i';
    return preg_replace($patron, '<span class="search-highlight">$1</span>', htmlspecialchars($texto));
}

// Manejar acciones
$action = $_GET['action'] ?? '';
$idTurno = $_GET['id'] ?? 0;

if ($action && $idTurno) {
    switch($action) {
        case 'confirmar':
            cambiarEstado($idTurno, 'confirmado');
            break;
        case 'cancelar':
            cambiarEstado($idTurno, 'cancelado');
            break;
        case 'completar':
            cambiarEstado($idTurno, 'completado');
            break;
        case 'eliminar':
            eliminarTurno($idTurno);
            break;
    }
}

// Funciones
function cambiarEstado($idTurno, $nuevoEstado) {
    global $link;
    $stmt = $link->prepare("UPDATE turno SET estado = ? WHERE idturno = ?");
    $stmt->bind_param("si", $nuevoEstado, $idTurno);
    $stmt->execute();
    $stmt->close();
    
    header("Location: panel.php?success=Estado actualizado");
    exit;
}

function eliminarTurno($idTurno) {
    global $link;
    $stmt = $link->prepare("DELETE FROM turno WHERE idturno = ?");
    $stmt->bind_param("i", $idTurno);
    $stmt->execute();
    $stmt->close();
    
    header("Location: panel.php?success=Turno eliminado");
    exit;
}

// Obtener filtros
$filtroEstado = $_GET['estado'] ?? '';
$filtroFecha = $_GET['fecha'] ?? '';
$filtroServicio = $_GET['servicio'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

// Construir consulta con filtros
$query = "SELECT t.*, c.nombre, c.apellido, c.telefono, c.email, 
                v.marca, v.modelo, v.patente, v.color, 
                s.nombre as servicio_nombre, s.descripcion as servicio_desc
        FROM turno t 
        JOIN cliente c ON t.idcliente = c.idcliente 
        JOIN vehiculo v ON t.idvehiculo = v.idvehiculo 
        JOIN servicio s ON t.idservicio = s.idservicio 
        WHERE 1=1";

$params = [];
$types = '';

// Aplicar filtros
if ($filtroEstado) {
    $query .= " AND t.estado = ?";
    $params[] = $filtroEstado;
    $types .= 's';
}

if ($filtroFecha) {
    $query .= " AND t.fechaReserva = ?";
    $params[] = $filtroFecha;
    $types .= 's';
}

if ($filtroServicio) {
    $query .= " AND s.nombre = ?";
    $params[] = $filtroServicio;
    $types .= 's';
}

if ($busqueda) {
    $query .= " AND (c.nombre LIKE ? OR c.apellido LIKE ? OR v.patente LIKE ? OR v.marca LIKE ? OR v.modelo LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sssss';
}

$query .= " ORDER BY t.fechaReserva DESC, t.horaReserva DESC";

// Ejecutar consulta
$stmt = $link->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Estadísticas SIMPLIFICADAS - Solo métricas útiles
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN estado = 'confirmado' THEN 1 ELSE 0 END) as confirmados,
    SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
    SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados
    FROM turno";
$statsResult = mysqli_query($link, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);

// Obtener lista de servicios para el filtro
$serviciosQuery = "SELECT DISTINCT nombre FROM servicio WHERE activo = 1";
$serviciosResult = mysqli_query($link, $serviciosQuery);
$servicios = [];
while ($row = mysqli_fetch_assoc($serviciosResult)) {
    $servicios[] = $row['nombre'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - VIP CAR WASH</title>
    <link rel='stylesheet' href='style.css'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
            --border: #bdc3c7;
            --shadow: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-hover: 0 8px 15px rgba(0,0,0,0.15);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--dark);
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            border-left: 5px solid var(--secondary);
            animation: slideInDown 0.5s ease;
        }

        .admin-header h1 {
            color: var(--primary);
            font-size: 2.2rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-header h1 i {
            color: var(--secondary);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border-top: 4px solid var(--secondary);
            animation: fadeInUp 0.6s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-card.pendientes { border-top-color: var(--warning); }
        .stat-card.confirmados { border-top-color: var(--success); }
        .stat-card.completados { border-top-color: var(--secondary); }
        .stat-card.cancelados { border-top-color: var(--danger); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .stat-card.pendientes .stat-number { color: var(--warning); }
        .stat-card.confirmados .stat-number { color: var(--success); }
        .stat-card.completados .stat-number { color: var(--secondary); }
        .stat-card.cancelados .stat-number { color: var(--danger); }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filters-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            animation: slideInUp 0.5s ease;
        }

        .filters-section h2 {
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.4rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .filter-input, .filter-select {
            padding: 12px 15px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            transition: var(--transition);
            background: white;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--secondary);
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-secondary {
            background: var(--gray);
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }

        .results-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            animation: fadeIn 0.6s ease;
        }

        .results-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .results-title {
            color: var(--primary);
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .results-count {
            background: var(--light);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            color: var(--dark);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        .data-table th {
            background: var(--primary);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .data-table tr:hover {
            background: #f8f9fa;
        }

        .estado-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .estado-pendiente { background: #fff3cd; color: #856404; }
        .estado-confirmado { background: #d1edff; color: #0c5460; }
        .estado-cancelado { background: #f8d7da; color: #721c24; }
        .estado-completado { background: #d4edda; color: #155724; }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .btn-confirmar { background: var(--success); color: white; }
        .btn-cancelar { background: var(--danger); color: white; }
        .btn-completar { background: var(--secondary); color: white; }
        .btn-eliminar { background: var(--gray); color: white; }

        .btn-action:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .search-highlight {
            background-color: #ffeb3b;
            padding: 1px 3px;
            border-radius: 3px;
            font-weight: bold;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }

        .no-results i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: var(--border);
        }

        .success-message {
            background: var(--success);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.5s ease;
        }

        /* Animaciones */
        @keyframes slideInDown {
            from { transform: translateY(-30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideInUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideInRight {
            from { transform: translateX(30px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeInUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-container {
                padding: 15px;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                justify-content: stretch;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }

            .stat-number {
                font-size: 2rem;
            }

            .data-table th,
            .data-table td {
                padding: 10px 8px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .admin-header h1 {
                font-size: 1.8rem;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <div class="admin-header">
            <h1>
                <i class="fas fa-car"></i>
                Panel de Administración - VIP CAR WASH
            </h1>
            <p style="color: var(--gray);">Gestión completa de turnos y reservas</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <!-- Estadísticas ÚTILES -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total'] ?? 0 ?></div>
                <div class="stat-label">Total Turnos</div>
                <div style="font-size: 0.7rem; margin-top: 5px; color: var(--gray);">
                    Todos los estados
                </div>
            </div>
            <div class="stat-card pendientes">
                <div class="stat-number"><?= $stats['pendientes'] ?? 0 ?></div>
                <div class="stat-label">Pendientes</div>
                <div style="font-size: 0.7rem; margin-top: 5px; color: var(--gray);">
                    Requieren atención
                </div>
            </div>
            <div class="stat-card confirmados">
                <div class="stat-number"><?= $stats['confirmados'] ?? 0 ?></div>
                <div class="stat-label">Confirmados</div>
                <div style="font-size: 0.7rem; margin-top: 5px; color: var(--gray);">
                    Próximos a realizar
                </div>
            </div>
            <div class="stat-card completados">
                <div class="stat-number"><?= $stats['completados'] ?? 0 ?></div>
                <div class="stat-label">Completados</div>
                <div style="font-size: 0.7rem; margin-top: 5px; color: var(--gray);">
                    Servicios realizados
                </div>
            </div>
            <div class="stat-card cancelados">
                <div class="stat-number"><?= $stats['cancelados'] ?? 0 ?></div>
                <div class="stat-label">Cancelados</div>
                <div style="font-size: 0.7rem; margin-top: 5px; color: var(--gray);">
                    Tasa de cancelación
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <h2><i class="fas fa-filter"></i> Filtros y Búsqueda</h2>
            
            <form method="GET" action="panel.php">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-tag"></i> Estado
                        </label>
                        <select name="estado" class="filter-select">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" <?= $filtroEstado == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="confirmado" <?= $filtroEstado == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                            <option value="cancelado" <?= $filtroEstado == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            <option value="completado" <?= $filtroEstado == 'completado' ? 'selected' : '' ?>>Completado</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-calendar"></i> Fecha
                        </label>
                        <input type="date" name="fecha" value="<?= $filtroFecha ?>" class="filter-input">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-concierge-bell"></i> Servicio
                        </label>
                        <select name="servicio" class="filter-select">
                            <option value="">Todos los servicios</option>
                            <?php foreach($servicios as $servicio): ?>
                                <option value="<?= htmlspecialchars($servicio) ?>" 
                                    <?= $filtroServicio == $servicio ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(ucfirst(str_replace('-', ' ', $servicio))) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">
                            <i class="fas fa-search"></i> Buscar
                        </label>
                        <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                            placeholder="Nombre, apellido, patente, marca..." class="filter-input">
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Aplicar Filtros
                    </button>
                    <a href="panel.php" class="btn btn-secondary">
                        <i class="fas fa-broom"></i>
                        Limpiar Filtros
                    </a>
                </div>
            </form>
        </div>

        <!-- Resultados -->
        <div class="results-section">
            <div class="results-header">
                <h3 class="results-title">
                    <i class="fas fa-list"></i>
                    Turnos Programados
                    <span class="results-count"><?= $result->num_rows ?> resultados</span>
                </h3>
            </div>

            <?php if($result->num_rows === 0): ?>
                <div class="no-results">
                    <i class="fas fa-inbox"></i>
                    <h3>No se encontraron turnos</h3>
                    <p>No hay turnos que coincidan con los filtros aplicados.</p>
                    <?php if ($busqueda || $filtroEstado || $filtroFecha || $filtroServicio): ?>
                        <a href="panel.php" style="color: var(--secondary); margin-top: 10px; display: inline-block;">
                            <i class="fas fa-eye"></i> Ver todos los turnos
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Vehículo</th>
                                <th>Servicio</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): 
                                $confirmado = $row['fecha_confirmacion'] ? date('d/m/Y H:i', strtotime($row['fecha_confirmacion'])) : 'No';
                            ?>
                            <tr>
                                <td><strong>#<?= $row['idturno'] ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($row['fechaReserva'])) ?></td>
                                <td><?= $row['horaReserva'] ?></td>
                                <td>
                                    <strong><?= resaltarBusqueda($row['nombre'], $busqueda) ?> <?= resaltarBusqueda($row['apellido'], $busqueda) ?></strong>
                                </td>
                                <td>
                                    <div><i class="fas fa-phone"></i> <?= htmlspecialchars($row['telefono']) ?></div>
                                    <div><i class="fas fa-envelope"></i> <?= htmlspecialchars($row['email']) ?></div>
                                </td>
                                <td>
                                    <?= resaltarBusqueda($row['marca'], $busqueda) ?> <?= resaltarBusqueda($row['modelo'], $busqueda) ?>
                                    <div style="font-size: 0.8rem; color: var(--gray);">
                                        <i class="fas fa-car"></i> <?= resaltarBusqueda($row['patente'], $busqueda) ?>
                                        <?php if($row['color']): ?>
                                            • <i class="fas fa-palette"></i> <?= htmlspecialchars($row['color']) ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($row['servicio_nombre']) ?></strong>
                                </td>
                                <td>
                                    <strong style="color: var(--success);">$<?= number_format($row['precio_final'], 0, ',', '.') ?></strong>
                                </td>
                                <td>
                                    <span class="estado-badge estado-<?= $row['estado'] ?>">
                                        <?= $row['estado'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if($row['estado'] == 'pendiente'): ?>
                                            <button class="btn-action btn-confirmar" onclick="cambiarEstado(<?= $row['idturno'] ?>, 'confirmar')">
                                                <i class="fas fa-check"></i> Confirmar
                                            </button>
                                            <button class="btn-action btn-cancelar" onclick="cambiarEstado(<?= $row['idturno'] ?>, 'cancelar')">
                                                <i class="fas fa-times"></i> Cancelar
                                            </button>
                                        <?php elseif($row['estado'] == 'confirmado'): ?>
                                            <button class="btn-action btn-completar" onclick="cambiarEstado(<?= $row['idturno'] ?>, 'completar')">
                                                <i class="fas fa-flag-checkered"></i> Completar
                                            </button>
                                            <button class="btn-action btn-cancelar" onclick="cambiarEstado(<?= $row['idturno'] ?>, 'cancelar')">
                                                <i class="fas fa-times"></i> Cancelar
                                            </button>
                                        <?php else: ?>
                                            <span style="color: var(--gray); font-size: 0.8rem;">No actions</span>
                                        <?php endif; ?>
                                        
                                        <button class="btn-action btn-eliminar" onclick="eliminarTurno(<?= $row['idturno'] ?>)">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function cambiarEstado(idTurno, accion) {
            if (confirm(`¿Estás seguro de que quieres ${accion.toUpperCase()} este turno?`)) {
                window.location.href = `panel.php?action=${accion}&id=${idTurno}`;
            }
        }
        
        function eliminarTurno(idTurno) {
            if (confirm('⚠️ ¿Estás seguro de que quieres ELIMINAR este turno? Esta acción no se puede deshacer.')) {
                window.location.href = `panel.php?action=eliminar&id=${idTurno}`;
            }
        }
        
        // Auto-ocultar mensaje de éxito después de 5 segundos
        setTimeout(() => {
            const successMsg = document.querySelector('.success-message');
            if (successMsg) {
                successMsg.style.display = 'none';
            }
        }, 5000);

        // Efectos de hover en tarjetas
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>

<?php
$stmt->close();
?>