<?php
// models/Usuario.php
// Modelo para manejar las operaciones de usuarios en la BD

class Usuario {
    // Propiedades de conexión y tabla
    private $conn;
    private $table_name = "usuario";

    // Propiedades del usuario
    public $id_usuario;
    public $username;
    public $password;
    public $email;

    /**
     * Constructor que recibe la conexión a BD
     * @param PDO $db
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Método para registrar un nuevo usuario
     * @return boolean
     */
    public function registrar() {
        // Query SQL para insertar usuario
        // :username, :password, :email son marcadores de posición
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    username = :username,
                    password = :password,
                    email = :email";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);

        // Limpiar los datos para evitar inyección SQL
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Encriptar la contraseña usando bcrypt
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        // Vincular los valores a los marcadores
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":email", $this->email);

        // Ejecutar la consulta
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    /**
     * Método para verificar credenciales de login
     * @return array|boolean
     */
    public function login() {
        // Query para buscar usuario por username
        $query = "SELECT id_usuario, username, password, email 
                  FROM " . $this->table_name . " 
                  WHERE username = :username 
                  LIMIT 1";

        // Preparar consulta
        $stmt = $this->conn->prepare($query);

        // Limpiar username
        $this->username = htmlspecialchars(strip_tags($this->username));

        // Vincular parámetro
        $stmt->bindParam(":username", $this->username);

        // Ejecutar consulta
        $stmt->execute();

        // Si encuentra el usuario
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar la contraseña con password_verify
            if(password_verify($this->password, $row['password'])) {
                // Contraseña correcta - retornar datos del usuario
                return $row;
            }
        }
        
        // Usuario no encontrado o contraseña incorrecta
        return false;
    }

    /**
     * Método para verificar si un username ya existe
     * @return boolean
     */
    public function existeUsername() {
        $query = "SELECT id_usuario FROM " . $this->table_name . " 
                  WHERE username = :username LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $this->username = htmlspecialchars(strip_tags($this->username));
        $stmt->bindParam(":username", $this->username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Método para verificar si un email ya existe
     * @return boolean
     */
    public function existeEmail() {
        $query = "SELECT id_usuario FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
?>