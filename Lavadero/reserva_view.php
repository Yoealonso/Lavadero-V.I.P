<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Turno - VIP Car Wash</title>
    <link rel='stylesheet' type='text/css' href='style.css'>
    <style>
        .alert { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .reserva-info { background: #e7f3ff; padding: 15px; margin: 15px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <header><h2>VIP CAR WASH</h2></header>
    
    <div class="container">
        <?php if (isset($successMessage)): ?>
            <div class="alert success">
                <?php echo $successMessage; ?>
                <?php if (isset($reservaData)): ?>
                    <div class="reserva-info">
                        <h4>📋 Resumen de tu reserva:</h4>
                        <p><strong>N° de reserva:</strong> #<?php echo $reservaData['id']; ?></p>
                        <p><strong>Cliente:</strong> <?php echo $reservaData['cliente']; ?></p>
                        <p><strong>Vehículo:</strong> <?php echo $reservaData['vehiculo']; ?> (<?php echo $reservaData['patente']; ?>)</p>
                        <p><strong>Fecha y hora:</strong> <?php echo $reservaData['fecha']; ?> a las <?php echo $reservaData['hora']; ?></p>
                        <p><strong>Servicio:</strong> <?php echo ucfirst($reservaData['servicio']); ?></p>
                        <p><strong>Precio estimado:</strong> $<?php echo number_format($reservaData['precio'], 0, ',', '.'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (isset($errorMessage)): ?>
            <div class="alert error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <?php if (!isset($successMessage)): ?>
        <form method="POST" action="reserva.php">
            <input type="hidden" name="servicio" value="<?php echo isset($_GET['servicio']) ? htmlspecialchars($_GET['servicio']) : 'basico'; ?>">
            
            <h3>Reservar Turno - Servicio <?php echo ucfirst(isset($_GET['servicio']) ? htmlspecialchars($_GET['servicio']) : 'Básico'); ?></h3>
            
            <fieldset>
                <legend>👤 Datos Personales</legend>
                <label>Nombre:*</label><input type="text" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"><br>
                <label>Apellido:*</label><input type="text" name="lastName" required value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>"><br>
                <label>Teléfono:*</label><input type="tel" name="phone" pattern="[0-9]{10,15}" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"><br>
                <label>Email:*</label><input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"><br>
                <label>Dirección:</label><input type="text" name="location" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>"><br>
            </fieldset>

            <fieldset>
                <legend>🚗 Datos del Vehículo</legend>
                <label>Marca:*</label>
                <select name="brand" required>
                    <option value="">Seleccione marca</option>
                    <option value="Ford" <?php echo (isset($_POST['brand']) && $_POST['brand'] == 'Ford') ? 'selected' : ''; ?>>Ford</option>
                    <option value="Chevrolet" <?php echo (isset($_POST['brand']) && $_POST['brand'] == 'Chevrolet') ? 'selected' : ''; ?>>Chevrolet</option>
                    <!-- Más opciones -->
                </select><br>
                
                <label>Tipo:*</label>
                <select name="type" required>
                    <option value="">Seleccione tipo</option>
                    <option value="auto" <?php echo (isset($_POST['type']) && $_POST['type'] == 'auto') ? 'selected' : ''; ?>>Auto</option>
                    <option value="camioneta" <?php echo (isset($_POST['type']) && $_POST['type'] == 'camioneta') ? 'selected' : ''; ?>>Camioneta</option>
                    <option value="suv" <?php echo (isset($_POST['type']) && $_POST['type'] == 'suv') ? 'selected' : ''; ?>>SUV</option>
                </select><br>
                
                <label>Modelo:*</label><input type="text" name="model" required value="<?php echo isset($_POST['model']) ? htmlspecialchars($_POST['model']) : ''; ?>"><br>
                <label>Año:*</label><input type="number" name="releaseDate" min="1950" max="2030" required value="<?php echo isset($_POST['releaseDate']) ? htmlspecialchars($_POST['releaseDate']) : '2010'; ?>"><br>
                <label>Patente:*</label><input type="text" name="patent" required value="<?php echo isset($_POST['patent']) ? htmlspecialchars($_POST['patent']) : ''; ?>"><br>
                <label>Detalles adicionales:</label><textarea name="details"><?php echo isset($_POST['details']) ? htmlspecialchars($_POST['details']) : ''; ?></textarea><br>
            </fieldset>

            <fieldset>
                <legend>📅 Datos del Turno</legend>
                <label>Fecha:*</label><input type="date" name="date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>"><br>
                <label>Hora:*</label>
                <select name="datetime" required>
                    <option value="">Seleccione hora</option>
                    <option value="09:00">09:00</option>
                    <option value="11:00">11:00</option>
                    <option value="14:00">14:00</option>
                    <option value="16:00">16:00</option>
                </select><br>
            </fieldset>

            <input type="submit" name="enviar" value="Confirmar Reserva" class="enviar">
        </form>
        <?php endif; ?>
    </div>
</body>
</html>