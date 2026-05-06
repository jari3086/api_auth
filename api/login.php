<?php
// api/login.php
// Endpoint para autenticar usuarios (inicio de sesión)

// Headers para API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir archivos necesarios
include_once '../config/database.php';
include_once '../models/Usuario.php';

// Obtener conexión a base de datos
$database = new Database();
$db = $database->getConnection();

// Instanciar objeto usuario
$usuario = new Usuario($db);

// Obtener datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que se recibieron username y password
if(
    !empty($data->username) &&
    !empty($data->password)
) {
    // Asignar valores al objeto
    $usuario->username = $data->username;
    $usuario->password = $data->password;

    // Intentar autenticar
    $result = $usuario->login();

    if($result) {
        // Autenticación exitosa
        http_response_code(200); // OK
        
        // Generar un token simple (en producción usar JWT)
        $token = bin2hex(random_bytes(32));
        
        echo json_encode(array(
            "success" => true,
            "message" => "Autenticación satisfactoria",
            "data" => array(
                "id" => $result['id'],
                "username" => $result['username'],
                "email" => $result['email'],
                "token" => $token // Token de sesión
            )
        ));
    } else {
        // Error de autenticación
        http_response_code(401); // Unauthorized
        echo json_encode(array(
            "success" => false,
            "message" => "Error en la autenticación. Usuario o contraseña incorrectos"
        ));
    }
} else {
    // Datos incompletos
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Datos incompletos. Se requiere username y password"
    ));
}
?>