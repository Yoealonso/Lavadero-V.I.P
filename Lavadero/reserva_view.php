<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Turno - VIP Car Wash</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='style.css'>
<style>
    /* === CONTENEDOR PRINCIPAL === */
.reserva-container {
    max-width: 800px;
    margin: 30px auto;
    padding: 25px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
    border: 1px solid #eee;
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* === TITULOS === */
.reserva-title {
    text-align: center;
    color: #b10606;
    margin-bottom: 25px;
    font-size: 24px;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* === FIELDSETS === */
.reserva-fieldset {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
    background-color: #fafafa;
    transition: box-shadow 0.3s ease;
}

.reserva-fieldset:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.08);
}

.reserva-fieldset legend {
    font-weight: 700;
    font-size: 18px;
    color: #b10606;
    padding: 0 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* === ETIQUETAS Y CAMPOS === */
.reserva-fieldset label {
    display: block;
    margin: 16px 0 6px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.reserva-fieldset input,
.reserva-fieldset select,
.reserva-fieldset textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 15px;
    background-color: #fff;
    transition: border-color 0.3s, box-shadow 0.3s;
    box-sizing: border-box;
}

.reserva-fieldset input:focus,
.reserva-fieldset select:focus,
.reserva-fieldset textarea:focus {
    outline: none;
    border-color: #b10606;
    box-shadow: 0 0 0 3px rgba(177, 6, 6, 0.15);
}

.reserva-fieldset textarea {
    min-height: 80px;
    resize: vertical;
}

/* === BOTÓN PRINCIPAL === */
.reserva-btn {
    display: block;
    width: 100%;
    padding: 14px;
    background-color: #b10606;
    color: white;
    font-size: 17px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s;
    margin-top: 10px;
    box-shadow: 0 4px 6px rgba(177, 6, 6, 0.2);
}

.reserva-btn:hover {
    background-color: #d60b0b;
    transform: translateY(-2px);
    box-shadow: 0 6px 10px rgba(177, 6, 6, 0.3);
}

.reserva-btn:active {
    transform: translateY(0);
}

/* === ALERTAS === */
.alert-modern {
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.alert-success {
    background-color: #f8fdf8;
    border: 1px solid #d4edda;
    color: #155724;
}

.alert-error {
    background-color: #fdf8f8;
    border: 1px solid #f8d7da;
    color: #721c24;
}

.alert-modern h2 {
    margin-top: 0;
    color: inherit;
}

.alert-modern p {
    margin: 10px 0;
    font-size: 16px;
    line-height: 1.5;
}

/* === RESPONSIVE === */
@media (max-width: 600px) {
    .reserva-container {
        margin: 15px;
        padding: 20px;
    }

    .reserva-title {
        font-size: 20px;
    }

    .reserva-fieldset {
        padding: 16px;
    }

    .reserva-btn {
        padding: 16px;
        font-size: 18px;
    }
}
</style>

</head>
<body>
    <header>
        <div class="logo"><h2>VIP CAR WASH</h2></div>
    </header>

    <nav>
        <a href="index.html" style="margin-right: 40px;" class="na"> Inicio</a>
        <a href="index.html#servicios" style="margin-right: 40px;" class="na"> Servicios</a>
        <a href="panel.php" class="admin-btn"> Admin</a>
    </nav>

    <div class="reserva-container fade-in-up">
        <?php if (!empty($successMessage)): ?>
            <div class="alert-modern alert-success">
                <h2><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></h2>
                <?php if (!empty($reservaData)): ?>
                    <p><strong>Reserva #</strong><?php echo intval($reservaData['id']); ?></p>
                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($reservaData['cliente'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Vehículo:</strong> <?php echo htmlspecialchars($reservaData['vehiculo'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($reservaData['patente'], ENT_QUOTES, 'UTF-8'); ?>)</p>
                    <p><strong>Fecha y hora:</strong> <?php echo htmlspecialchars($reservaData['fecha_formateada'], ENT_QUOTES, 'UTF-8'); ?> a las <?php echo htmlspecialchars($reservaData['hora'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Servicio:</strong> <?php echo htmlspecialchars($reservaData['servicio_nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Precio estimado:</strong> $<?php echo number_format(floatval($reservaData['precio_final'] ?? 0), 0, ',', '.'); ?></p>
                    <p><em>Revisa tu WhatsApp para confirmar el turno</em></p>
                <?php endif; ?>
                <a href="index.html" class="reserva-btn">Volver al Inicio</a>
            </div>

        <?php elseif (!empty($errorMessage)): ?>
            <div class="alert-modern alert-error">
                <h2>Error</h2>
                <p><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                <a href="reserva.php?servicio=<?php echo htmlspecialchars($_GET['servicio'] ?? 'pre-venta-basic', ENT_QUOTES, 'UTF-8'); ?>" class="reserva-btn">Intentar nuevamente</a>
            </div>

        <?php else: ?>
            <form method="POST" action="reserva.php" id="reservaForm" autocomplete="on">
                <input type="hidden" name="servicio" value="<?php echo htmlspecialchars($_GET['servicio'] ?? 'pre-venta-basic', ENT_QUOTES, 'UTF-8'); ?>">

                <h1 class="reserva-title">Reservar Turno - 
                    <?php 
                    $servicios_nombres = [
                        'pre-venta-basic' => 'Pre Venta Basic',
                        'pre-venta-premium' => 'Pre Venta Premium',
                        'lavado-premium-auto' => 'Lavado Premium Auto',
                        'lavado-premium-camioneta' => 'Lavado Premium Camioneta',
                        'lavado-premium-suv' => 'Lavado Premium SUV',
                        'lavado-vip-extreme' => 'Lavado VIP Extreme',
                        'tratamiento-ceramico' => 'Tratamiento Ceramico',
                        'abrillantado-carroceria' => 'Abrillantado de Carroceria',
                        'limpieza-motor' => 'Limpieza de Motor',
                        'pulido-opticas' => 'Pulido de Opticas',
                        'pintura-llantas' => 'Pintura de Llantas',
                        'limpieza-tapizados' => 'Limpieza de Tapizados'
                    ];
                    echo htmlspecialchars($servicios_nombres[$_GET['servicio'] ?? 'pre-venta-basic'] ?? 'Servicio');
                    ?>
                </h1>

                <fieldset class="reserva-fieldset">
                    <legend>Datos Personales</legend>
                    
                    <label for="name">Nombre *</label>
                    <input id="name" name="name" type="text" required maxlength="50" placeholder="Tu nombre">

                    <label for="lastName">Apellido *</label>
                    <input id="lastName" name="lastName" type="text" required maxlength="50" placeholder="Tu apellido">

                    <label for="phone">Teléfono *</label>
                    <input id="phone" name="phone" type="tel" required pattern="[0-9+\-\s]{6,20}" maxlength="20" placeholder="Ej: 2291-416897">

                    <label for="email">Email *</label>
                    <input id="email" name="email" type="email" required maxlength="100" placeholder="tu@email.com">

                    <label for="location">Dirección</label>
                    <input id="location" name="location" type="text" maxlength="200" placeholder="Tu dirección">
                </fieldset>

                <fieldset class="reserva-fieldset">
                    <legend>Datos del Vehículo</legend>

                    <label for="brand">Marca *</label>
                    <input id="brand" name="brand" type="text" required maxlength="50" placeholder="Ej: Ford, Toyota">

                    <label for="type">Tipo *</label>
                    <select id="type" name="type" required>
                        <option value="auto">Auto</option>
                        <option value="camioneta">Camioneta</option>
                        <option value="suv">SUV</option>
                    </select>

                    <label for="model">Modelo *</label>
                    <input id="model" name="model" type="text" required maxlength="50" placeholder="Ej: Focus, Corolla">

                    <label for="releaseDate">Año</label>
                    <input id="releaseDate" name="releaseDate" type="number" min="1900" max="<?php echo date('Y')+1; ?>" placeholder="Ej: 2020">

                    <label for="patent">Patente *</label>
                    <input id="patent" name="patent" type="text" required maxlength="15" style="text-transform:uppercase" placeholder="Ej: AB123CD">

                    <label for="color">Color *</label>
                    <input id="color" name="color" type="text" required maxlength="30" placeholder="Ej: Rojo, Azul, Negro">

                    <?php if (($_GET['servicio'] ?? '') === 'limpieza-tapizados'): ?>
                    <label for="plazas">Número de Plazas *</label>
                    <select id="plazas" name="plazas" required>
                        <option value="4">4 plazas</option>
                        <option value="7">7 plazas</option>
                    </select>
                    <?php else: ?>
                    <input type="hidden" name="plazas" value="4">
                    <?php endif; ?>

                    <label for="details">Detalles adicionales</label>
                    <textarea id="details" name="details" maxlength="500" placeholder="Observaciones sobre el vehículo..."></textarea>
                </fieldset>

                <fieldset class="reserva-fieldset">
                    <legend>Fecha y Hora del Turno</legend>

                    <label for="date">Fecha *</label>
                    <input id="date" name="date" type="date" required>

                    <label for="datetime">Hora *</label>
                    <input id="datetime" name="datetime" type="time" required>
                </fieldset>

                <button type="submit" class="reserva-btn">Confirmar reserva</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        (function(){
            const d = new Date();
            d.setDate(d.getDate() + 1);
            const min = d.toISOString().split('T')[0];
            const dateInput = document.getElementById('date');
            if (dateInput) dateInput.min = min;
        })();
    </script>
</body>
</html>