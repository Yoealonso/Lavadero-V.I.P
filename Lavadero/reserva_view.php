<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Turno - VIP Car Wash</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='style.css'>
</head>
<body>
    <header>
        <div class="logo"><h2>VIP CAR WASH</h2></div>
    </header>

    <nav>
        <a href="index.html" style="margin-right: 40px;" class="na"> Inicio</a>
        <a href="#servicios" style="margin-right: 40px;" class="na"> Servicios</a>
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
        // Forzar fecha mínima (mañana)
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