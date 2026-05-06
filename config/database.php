<?php
// config/database.php
// Configuración de conexión a la base de datos MySQL

class Database {
    // Propiedades de conexión
    private $host = "localhost";     // Servidor de BD
    private $db_name = "api_auth_db"; // Nombre de la BD
    private $username = "root";      // Usuario por defecto XAMPP
    private $password = "";          // Contraseña vacía en XAMPP
    public $conn;                    // Objeto de conexión

    /**
     * Método para obtener la conexión a la base de datos
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
            // Crear nueva conexión PDO
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            
            // Configurar PDO para que lance excepciones en errores
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Establecer el conjunto de caracteres a UTF-8
            $this->conn->exec("set names utf8");
            
        } catch(PDOException $exception) {
            // Si hay error, mostrar mensaje
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>