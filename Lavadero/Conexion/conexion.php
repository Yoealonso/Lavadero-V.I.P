<?php
class Database {
    private static $instance = null;
    private $link;
    
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "lavadero";
    
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        $this->link = new mysqli($this->host, $this->user, $this->password, $this->database);
        
        if ($this->link->connect_error) {
            error_log("Error de conexión a la BD: " . $this->link->connect_error);
            throw new Exception("Error de conexión a la base de datos. Por favor, intente más tarde.");
        }
        
        $this->link->set_charset("utf8mb4");
    }
    
    public function getConnection() {
        return $this->link;
    }
    
    private function __clone() {}
}

// Crear instancia global para compatibilidad
try {
    $database = Database::getInstance();
    $link = $database->getConnection();
} catch (Exception $e) {
    die("Error del sistema: " . $e->getMessage());
}
?>