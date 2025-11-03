<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar Turno - VIP Car Wash</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='style.css'>
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
            --shadow: 0 8px 25px rgba(0,0,0,0.1);
            --shadow-hover: 0 15px 35px rgba(0,0,0,0.15);
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-dark: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--gradient);
            min-height: 100vh;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Header mejorado */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-bottom: 3px solid var(--secondary);
        }

        .logo h2 {
            color: var(--primary);
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            background: linear-gradient(45deg, var(--secondary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        /* Navegación mejorada */
        nav {
            background: rgba(44, 62, 80, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        nav a:hover::before {
            left: 100%;
        }

        nav a:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .admin-btn {
            background: var(--secondary);
            margin-left: auto;
        }

        .admin-btn:hover {
            background: #2980b9;
        }

        /* Contenedor principal */
        .reserva-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
            animation: fadeInUp 0.8s ease;
        }

        /* Alertas modernas */
        .alert-modern {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
            text-align: center;
            border-left: 6px solid;
            animation: slideInRight 0.6s ease;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .alert-success {
            border-left-color: var(--success);
            background: linear-gradient(135deg, #d4edda, #ffffff);
        }

        .alert-error {
            border-left-color: var(--danger);
            background: linear-gradient(135deg, #f8d7da, #ffffff);
        }

        .alert-modern h2 {
            color: var(--dark);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .alert-modern p {
            margin-bottom: 1rem;
            color: var(--dark);
            font-size: 1.1rem;
        }

        .alert-modern strong {
            color: var(--primary);
        }

        /* Formulario ÉPICO */
        .reserva-title {
            text-align: center;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            background: linear-gradient(45deg, #ffffff, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { text-shadow: 0 0 10px rgba(255,255,255,0.5); }
            to { text-shadow: 0 0 20px rgba(255,255,255,0.8); }
        }

        form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            padding: 3rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(255,255,255,0.2);
            animation: formSlideIn 0.8s ease;
        }

        @keyframes formSlideIn {
            from { 
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to { 
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .reserva-fieldset {
            border: none;
            margin-bottom: 2.5rem;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid var(--secondary);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .reserva-fieldset::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .reserva-fieldset:hover::before {
            transform: scaleX(1);
        }

        .reserva-fieldset:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .reserva-fieldset legend {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary);
            padding: 0 1rem;
            background: var(--gradient-dark);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        /* Labels e inputs mejorados */
        label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 600;
            color: var(--dark);
            font-size: 1rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        label::before {
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--secondary);
            font-size: 0.9rem;
        }

        label[for="name"]::before { content: '\f007'; }
        label[for="lastName"]::before { content: '\f2bd'; }
        label[for="phone"]::before { content: '\f095'; }
        label[for="email"]::before { content: '\f0e0'; }
        label[for="location"]::before { content: '\f3c5'; }
        label[for="brand"]::before { content: '\f1b9'; }
        label[for="type"]::before { content: '\f1b9'; }
        label[for="model"]::before { content: '\f1b9'; }
        label[for="releaseDate"]::before { content: '\f073'; }
        label[for="patent"]::before { content: '\f1cd'; }
        label[for="color"]::before { content: '\f53f'; }
        label[for="plazas"]::before { content: '\f0c0'; }
        label[for="details"]::before { content: '\f0f6'; }
        label[for="date"]::before { content: '\f073'; }
        label[for="datetime"]::before { content: '\f017'; }

        input, select, textarea {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
            background: white;
            color: var(--dark);
            margin-bottom: 1.5rem;
            font-family: inherit;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            transform: translateY(-2px);
        }

        input:hover, select:hover, textarea:hover {
            border-color: var(--secondary);
        }

        /* Botón ÉPICO */
        .reserva-btn {
            background: var(--gradient-dark);
            color: white;
            border: none;
            padding: 1.2rem 3rem;
            font-size: 1.2rem;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            transition: var(--transition);
            display: block;
            margin: 2rem auto 0;
            width: fit-content;
            min-width: 250px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        }

        .reserva-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .reserva-btn:hover::before {
            left: 100%;
        }

        .reserva-btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 35px rgba(52, 152, 219, 0.4);
        }

        .reserva-btn:active {
            transform: translateY(-2px) scale(1.02);
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .reserva-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }

            form {
                padding: 1.5rem;
            }

            .reserva-title {
                font-size: 2rem;
            }

            .reserva-fieldset {
                padding: 1.5rem;
            }

            nav {
                flex-direction: column;
                gap: 0.5rem;
                padding: 1rem;
            }

            .admin-btn {
                margin-left: 0;
            }

            .reserva-btn {
                width: 100%;
                min-width: auto;
            }
        }

        @media (max-width: 480px) {
            .reserva-title {
                font-size: 1.6rem;
            }

            .alert-modern {
                padding: 1.5rem;
            }

            form {
                padding: 1rem;
            }

            .reserva-fieldset {
                padding: 1rem;
            }

            input, select, textarea {
                padding: 0.8rem;
            }
        }

        /* Efectos especiales */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(52, 152, 219, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0); }
        }

        /* Mejora para selects */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }

        /* Mejora para textarea */
        textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        /* Indicador de campos requeridos */
        label:has(+ input:required)::after,
        label:has(+ select:required)::after {
            content: '*';
            color: var(--danger);
            margin-left: 0.3rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo"><h2>🚗 VIP CAR WASH</h2></div>
    </header>

    <nav>
        <a href="index.html" class="na">
            <i class="fas fa-home"></i> Inicio
        </a>
        <a href="#servicios" class="na">
            <i class="fas fa-concierge-bell"></i> Servicios
        </a>
        <a href="panel.php" class="admin-btn">
            <i class="fas fa-cog"></i> Panel Admin
        </a>
    </nav>

    <div class="reserva-container">
        <?php if (!empty($successMessage)): ?>
            <div class="alert-modern alert-success floating">
                <h2><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></h2>
                <?php if (!empty($reservaData)): ?>
                    <div style="text-align: left; background: rgba(255,255,255,0.8); padding: 1.5rem; border-radius: 10px; margin: 1.5rem 0;">
                        <p><strong>📋 Reserva #</strong><?php echo intval($reservaData['id']); ?></p>
                        <p><strong>👤 Cliente:</strong> <?php echo htmlspecialchars($reservaData['cliente'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>🚗 Vehículo:</strong> <?php echo htmlspecialchars($reservaData['vehiculo'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($reservaData['patente'], ENT_QUOTES, 'UTF-8'); ?>)</p>
                        <p><strong>📅 Fecha y hora:</strong> <?php echo htmlspecialchars($reservaData['fecha_formateada'], ENT_QUOTES, 'UTF-8'); ?> a las <?php echo htmlspecialchars($reservaData['hora'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p><strong>🔧 Servicio:</strong> <?php echo htmlspecialchars($reservaData['servicio_nombre'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php if ($reservaData['precio_final'] > 0): ?>
                            <p><strong>💰 Precio estimado:</strong> $<?php echo number_format(floatval($reservaData['precio_final']), 0, ',', '.'); ?></p>
                        <?php else: ?>
                            <p><strong>💰 Precio:</strong> <em style="color: var(--warning);">📞 Consultar presupuesto - Nos contactaremos para cotizar</em></p>
                        <?php endif; ?>
                    </div>
                    <p><i class="fas fa-whatsapp"></i> <em>Revisa tu WhatsApp para confirmar el turno</em></p>
                <?php endif; ?>
                <a href="index.html" class="reserva-btn pulse" style="margin-top: 1.5rem;">
                    <i class="fas fa-arrow-left"></i> Volver al Inicio
                </a>
            </div>

        <?php elseif (!empty($errorMessage)): ?>
            <div class="alert-modern alert-error">
                <h2><i class="fas fa-exclamation-triangle"></i> Error</h2>
                <p><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
                <a href="reserva.php?servicio=<?php echo htmlspecialchars($_GET['servicio'] ?? 'pre-venta-basic', ENT_QUOTES, 'UTF-8'); ?>" class="reserva-btn">
                    <i class="fas fa-redo"></i> Intentar nuevamente
                </a>
            </div>

        <?php else: ?>
            <form method="POST" action="reserva.php" id="reservaForm" autocomplete="on">
                <input type="hidden" name="servicio" value="<?php echo htmlspecialchars($_GET['servicio'] ?? 'pre-venta-basic', ENT_QUOTES, 'UTF-8'); ?>">

                <h1 class="reserva-title floating">
                    <i class="fas fa-calendar-plus"></i> Reservar Turno - 
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
                    <legend><i class="fas fa-user"></i> Datos Personales</legend>
                    
                    <label for="name">Nombre</label>
                    <input id="name" name="name" type="text" required maxlength="50" placeholder="Ingresa tu nombre completo">

                    <label for="lastName">Apellido</label>
                    <input id="lastName" name="lastName" type="text" required maxlength="50" placeholder="Ingresa tu apellido">

                    <label for="phone">Teléfono</label>
                    <input id="phone" name="phone" type="tel" required pattern="[0-9+\-\s]{6,20}" maxlength="20" placeholder="Ej: 2291-416897">

                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required maxlength="100" placeholder="tu@email.com">

                    <label for="location">Dirección</label>
                    <input id="location" name="location" type="text" maxlength="200" placeholder="Tu dirección completa">
                </fieldset>

                <fieldset class="reserva-fieldset">
                    <legend><i class="fas fa-car"></i> Datos del Vehículo</legend>

                    <label for="brand">Marca</label>
                    <input id="brand" name="brand" type="text" required maxlength="50" placeholder="Ej: Ford, Toyota, Volkswagen">

                    <label for="type">Tipo de Vehículo</label>
                    <select id="type" name="type" required>
                        <option value="">Selecciona el tipo</option>
                        <option value="auto">🚗 Auto</option>
                        <option value="camioneta">🚙 Camioneta</option>
                        <option value="suv">🚘 SUV</option>
                    </select>

                    <label for="model">Modelo</label>
                    <input id="model" name="model" type="text" required maxlength="50" placeholder="Ej: Focus, Corolla, Gol">

                    <label for="releaseDate">Año</label>
                    <input id="releaseDate" name="releaseDate" type="number" min="1900" max="<?php echo date('Y')+1; ?>" placeholder="Ej: 2020">

                    <label for="patent">Patente</label>
                    <input id="patent" name="patent" type="text" required maxlength="15" style="text-transform:uppercase" placeholder="Ej: AB123CD">

                    <label for="color">Color</label>
                    <input id="color" name="color" type="text" required maxlength="30" placeholder="Ej: Rojo, Azul, Negro, Blanco">

                    <?php if (($_GET['servicio'] ?? '') === 'limpieza-tapizados'): ?>
                    <label for="plazas">Número de Plazas</label>
                    <select id="plazas" name="plazas" required>
                        <option value="">Selecciona plazas</option>
                        <option value="4">4 plazas</option>
                        <option value="7">7 plazas</option>
                    </select>
                    <?php else: ?>
                    <input type="hidden" name="plazas" value="4">
                    <?php endif; ?>

                    <label for="details">Detalles adicionales</label>
                    <textarea id="details" name="details" maxlength="500" placeholder="Observaciones sobre el vehículo, detalles especiales, etc..."></textarea>
                </fieldset>

                <fieldset class="reserva-fieldset">
                    <legend><i class="fas fa-clock"></i> Fecha y Hora del Turno</legend>

                    <label for="date">Fecha</label>
                    <input id="date" name="date" type="date" required>

                    <label for="datetime">Hora</label>
                    <input id="datetime" name="datetime" type="time" required>
                </fieldset>

                <button type="submit" class="reserva-btn pulse">
                    <i class="fas fa-check-circle"></i> Confirmar Reserva
                </button>
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

        // Efectos interactivos adicionales
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Efecto de escritura para el título
            const title = document.querySelector('.reserva-title');
            if (title) {
                const text = title.textContent;
                title.textContent = '';
                let i = 0;
                
                function typeWriter() {
                    if (i < text.length) {
                        title.textContent += text.charAt(i);
                        i++;
                        setTimeout(typeWriter, 50);
                    }
                }
                
                setTimeout(typeWriter, 1000);
            }
        });
    </script>
</body>
</html>