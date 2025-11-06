<?php
// Si el archivo solicitado existe en el directorio actual, se muestra normalmente
if (php_sapi_name() == 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

// Redirige siempre al index.html (útil para SPA o proyectos mixtos)
require_once __DIR__ . 'Lavadero/index.html';


