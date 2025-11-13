<?php
// panel.php - Panel de administración avanzado
include_once "./Conexion/conexion.php";

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
    
    // Redirigir para evitar reenvío de formulario
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
    $query .= " AND (c.nombre LIKE ? OR c.apellido LIKE ? OR v.patente LIKE ? OR v.marca LIKE ?)";
    $searchTerm = "%$busqueda%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ssss';
}

$query .= " ORDER BY t.fechaReserva DESC, t.horaReserva DESC";

// Ejecutar consulta
$stmt = $link->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Estadísticas
$statsQuery = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN estado = 'confirmado' THEN 1 ELSE 0 END) as confirmados,
    SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
    SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
    SUM(precio_final) as ingresos_totales
    FROM turno";
$statsResult = mysqli_query($link, $statsQuery);
$stats = mysqli_fetch_assoc($statsResult);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel Admin Avanzado - VIP CAR WASH</title>
    <link rel='stylesheet' href='style.css'>
    <style>
        .estado-pendiente { background-color: #fff3cd; }
        .estado-confirmado { background-color: #d1edff; }
        .estado-cancelado { background-color: #f8d7da; }
        .estado-completado { background-color: #d4edda; }
        
        .filtros-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #b10606ff;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #b10606ff;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        
        .btn-action {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-confirmar { background: #28a745; color: white; }
        .btn-cancelar { background: #dc3545; color: white; }
        .btn-completar { background: #17a2b8; color: white; }
        .btn-eliminar { background: #6c757d; color: white; }
        
        .btn-action:hover {
            opacity: 0.8;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <header><h2>🚗 PANEL ADMINISTRADOR AVANZADO - VIP CAR WASH</h2></header>
    
    <div style="padding: 20px;">
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                ✅ <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <!-- ESTADÍSTICAS -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total'] ?? 0 ?></div>
                <div class="stat-label">Total Turnos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['pendientes'] ?? 0 ?></div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['confirmados'] ?? 0 ?></div>
                <div class="stat-label">Confirmados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['completados'] ?? 0 ?></div>
                <div class="stat-label">Completados</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?= number_format($stats['ingresos_totales'] ?? 0, 0, ',', '.') ?></div>
                <div class="stat-label">Ingresos Totales</div>
            </div>
        </div>

        <!-- FILTROS -->
        <div class="filtros-container">
            <h3>🔍 Filtros y Búsqueda</h3>

            
    <style>
        /* === ESTILOS DE FILTROS === */
        .filtros-container {
            margin-top: 10px;
        }

        .filtros-form {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            align-items: end;
            margin-top: 10px;
        }

        .filtros-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #990505ff;
            text-shadow: 0 0 3px rgba(0, 0, 0, 0.6);
        }

        .filtros-form select,
        .filtros-form input[type="date"],
        .filtros-form input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #444;
            border-radius: 4px;
            background-color: #4b4848ff;
            color: #fff;
            box-sizing: border-box;
            transition: border 0.2s ease-in-out;
        }
          .filtros-form select:focus,
    .filtros-form input:focus {
        border-color: #b10606;
        outline: none;
    }

/* === BOTONES === */
.botones-filtros {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-top: 15px;
    width: 100%;
    min-width: 0;
}

/* columnas de los botones */
.col-aplicar,
.col-limpiar {
    flex: 1;
    min-width: 0;
    display: flex;
    justify-content: center;
    align-items:center;
}

/* estilo base de botones */
.btn-aplicar,
.btn-limpiar {
    width: 100%;
    padding: 8px 10px;
    border-radius: 4px;
    text-align: center;
    font-size: 13px;
    font-weight: 500;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background 0.3s ease;
    box-sizing: border-box;
    white-space: nowrap;
    line-height: 1.4;
}

/* aplicar */
.btn-aplicar {
    background-color: #b10606;
}

.btn-aplicar:hover {
    background-color: #d60b0b;
}

/* limpiar */
.btn-limpiar {
    background-color: #6c757d;
    text-decoration: none;
    display: inline-block;
}

.btn-limpiar:hover {
    background-color: #81878c;
}

/* === RESPONSIVE === */
@media (max-width: 600px) {
    .botones-filtros {
        flex-direction: column;
        align-items: stretch;
    }

    .col-aplicar,
    .col-limpiar {
        width: 100%;
    }

    .col-aplicar {
        margin-bottom: 10px; /* espacio entre botones */
    }
}


    </style>

            <form method="GET" action="panel.php" class="filtros-form">
                <div>
                    <label><strong>Estado:</strong></label>
                    <select name="estado">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?= $filtroEstado == 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="confirmado" <?= $filtroEstado == 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                        <option value="cancelado" <?= $filtroEstado == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        <option value="completado" <?= $filtroEstado == 'completado' ? 'selected' : '' ?>>Completado</option>
                    </select>
                </div>
                
                <div>
                    <label><strong>Fecha:</strong></label>
                    <input type="date" name="fecha" value="<?= $filtroFecha ?>">
                </div>
                
                <div>
                    <label><strong>Servicio:</strong></label>
                    <select name="servicio" >
                        <option value="">Todos los servicios</option>
                        <option value="basico" <?= $filtroServicio == 'basico' ? 'selected' : '' ?>>Básico</option>
                        <option value="premium" <?= $filtroServicio == 'premium' ? 'selected' : '' ?>>Premium</option>
                        <option value="full" <?= $filtroServicio == 'full' ? 'selected' : '' ?>>Full</option>
                    </select>
                </div>
                
                <div>
                    <label><strong>Buscar:</strong></label>
                    <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" 
                        placeholder="Nombre, apellido, patente..." >
                </div>
                
                <div class="botones-filtros">
                    <div class="col-aplicar">
                        <button type="submit" class="btn-aplicar">🔍 Aplicar Filtros</button>
                    </div>
                    <div class="col-limpiar">
                    <a href="panel.php" class="btn-limpiar">🗑️Limpiar</a>
                </div>
                </div>
            </form>
        </div>

        <!-- TABLA DE TURNOS -->
        <h3>📋 Turnos Programados (<?= $result->num_rows ?> resultados)</h3>
        
        <?php if($result->num_rows === 0): ?>
            <div style="text-align: center; padding: 40px; color: #666; background: #f8f9fa; border-radius: 10px;">
                <h4>📭 No se encontraron turnos</h4>
                <p>No hay turnos que coincidan con los filtros aplicados.</p>
            </div>
        <?php else: ?>
            <table border="1" style="width:100%; border-collapse:collapse; margin-top: 20px; font-size: 14px;">
                <thead>
                    <tr style="background-color: #b10606ff; color: white;">
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Cliente</th>
                        <th>Contacto</th>
                        <th>Vehículo</th>
                        <th>Color</th>
                        <th>Servicio</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Confirmado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): 
                        $estadoClass = 'estado-' . $row['estado'];
                        $confirmado = $row['fecha_confirmacion'] ? date('d/m/Y H:i', strtotime($row['fecha_confirmacion'])) : 'No';
                    ?>
                    <tr class="<?= $estadoClass ?>">
                        <td><?= date('d/m/Y', strtotime($row['fechaReserva'])) ?></td>
                        <td><?= $row['horaReserva'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['nombre']) ?> <?= htmlspecialchars($row['apellido']) ?></strong>
                        </td>
                        <td>
                            📞 <?= htmlspecialchars($row['telefono']) ?><br>
                            📧 <?= htmlspecialchars($row['email']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['marca']) ?> <?= htmlspecialchars($row['modelo']) ?><br>
                            <small>(<?= htmlspecialchars($row['patente']) ?>)</small>
                        </td>
                        <td><?= htmlspecialchars($row['color']) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['servicio_nombre']) ?></strong><br>
                            <small><?= htmlspecialchars($row['servicio_desc']) ?></small>
                        </td>
                        <td><strong>$<?= number_format($row['precio_final'], 0, ',', '.') ?></strong></td>
                        <td>
                            <strong style="text-transform: uppercase;"><?= $row['estado'] ?></strong>
                        </td>
                        <td><?= $confirmado ?></td>
                        <td style="text-align: center;">
                            <?php if($row['estado'] == 'pendiente'): ?>
                                <button class="btn-action btn-confirmar" onclick="cambiarEstado(<?= $row['idturno'] ?>, 'confirmar')">
                                    ✅ Confirmar
                                </button>
                                <button class="btn-action btn-cancelar" onclick="cambiarEstado(<?= $row['idturno'] ?>, 'cancelar')">
                                    ❌ Cancelar
                                </button>
                            <?php elseif($row['estado'] == 'confirmado'): ?>
                                <button class="btn-action btn-completar" onclick="cambiarEstado(<?= $row['idturno'] ?>, 'completar')">
                                    🏁 Completar
                                </button>
                                <button class="btn-action btn-cancelar" onclick="cambiarEstado(<?= $row['idturno'] ?>, 'cancelar')">
                                    ❌ Cancelar
                                </button>
                            <?php else: ?>
                                <span style="color: #666;">-</span>
                            <?php endif; ?>
                            
                            <br>
                            <button class="btn-action btn-eliminar" onclick="eliminarTurno(<?= $row['idturno'] ?>)" 
                                    style="margin-top: 5px; background: #dc3545;">
                                🗑️ Eliminar
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
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
    </script>
</body>
</html>

<?php
$stmt->close();
?>