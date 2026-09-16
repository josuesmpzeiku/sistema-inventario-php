<?php
/**
 * Clase de Conexión a Base de Datos (Patrón Singleton)
 * Ubicación: config/Database.php
 */

class Database {
    private static ?Database $instance = null;
    private ?PDO $conn = null;

    // Cambia estos datos según tu servidor local
    private string $host = 'localhost';
    private string $db_name = 'optica';
    private string $user = 'Miguel';
    private string $password = 'Zeiku+2021'; 
    private string $charset = 'utf8mb4';

    // El constructor privado evita la clonación externa
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            // Error controlado para seguridad (ZAP / Mozilla)
            // No exponemos detalles internos en producción
            http_response_code(500);
            echo json_encode(["error" => "Error interno de configuración en el servidor."]);
            exit();
        }
    }

    // Obtener la instancia única de la clase
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Obtener el objeto de conexión PDO
    public function getConnection(): PDO {
        return $this->conn;
    }

    // Evitar que se clone la instancia
    private function __clone() {}
}