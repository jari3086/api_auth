<?php
// api/register.php
// Endpoint para registrar nuevos usuarios

// Headers necesarios para API REST
// Permite acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
// Especifica que el contenido será JSON
header("Content-Type: application/json; charset=UTF-8");
// Método HTTP permitido
header("Access-Control-Allow-Methods: POST");
// Tiempo máximo de cache
header("Access-Control-Max-Age: 3600");
// Headers permitidos
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir archivos necesarios
include_once '../config/database.php';  // Conexión a BD
include_once '../models/Usuario.php';    // Modelo de usuario

// Instanciar base de datos y obtener conexión
$database = new Database();
$db = $database->getConnection();

// Instanciar objeto usuario
$usuario = new Usuario($db);

// Obtener datos enviados por POST
$data = json_decode(file_get_contents("php://input"));

// Verificar que se recibieron todos los datos necesarios
if(
    !empty($data->username) &&
    !empty($data->password) &&
    !empty($data->email)
) {
    // Asignar valores al objeto usuario
    $usuario->username = $data->username;
    $usuario->password = $data->password;
    $usuario->email = $data->email;

    // Verificar si el username ya existe
    if($usuario->existeUsername()) {
        // Username ya registrado
        http_response_code(400); // Bad Request
        echo json_encode(array(
            "success" => false,
            "message" => "El nombre de usuario ya existe"
        ));
        exit;
    }

    // Verificar si el email ya existe
    if($usuario->existeEmail()) {
        // Email ya registrado
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "El email ya está registrado"
        ));
        exit;
    }

    // Intentar registrar el usuario
    if($usuario->registrar()) {
        // Registro exitoso
        http_response_code(201); // Created
        echo json_encode(array(
            "success" => true,
            "message" => "Usuario registrado exitosamente",
            "data" => array(
                "username" => $usuario->username,
                "email" => $usuario->email
            )
        ));
    } else {
        // Error al registrar
        http_response_code(503); // Service Unavailable
        echo json_encode(array(
            "success" => false,
            "message" => "No se pudo registrar el usuario"
        ));
    }
} else {
    // Datos incompletos
    http_response_code(400);
    echo json_encode(array(
        "success" => false,
        "message" => "Datos incompletos. Se requiere username, password y email"
    ));
}
?>