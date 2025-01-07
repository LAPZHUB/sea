<?php
require_once 'db.php'; // Asegura la conexión a la base de datos

// Iniciar sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización y validación de entradas
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (!$username || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'Credenciales inválidas']);
        exit;
    }

    try {
        // Consulta para obtener el usuario
        $query = $conn->prepare("SELECT * FROM dirigente WHERE usuario = :username");
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        // Validar credenciales
        if ($user && password_verify($password, $user['password'])) {
            if ($user['activo'] == 1) { // Verificar si el usuario está activo
                $_SESSION['user_id'] = $user['id_dirigente'];
                $_SESSION['role'] = 'dirigente'; // Puedes ajustar esto según tu sistema de roles
                echo json_encode(['status' => 'success', 'message' => 'Login exitoso']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Usuario inactivo. Contacte al administrador.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Credenciales inválidas']);
        }
    } catch (PDOException $e) {
        error_log("Error en la autenticación: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Ocurrió un error en el sistema. Intente más tarde.']);
    }
}
?>