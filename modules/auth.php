<?php
require_once '../includes/db.php'; // Asegura la conexión a la base de datos

// Iniciar sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización y validación de entradas
    error_log(print_r($_POST, true));
    echo json_encode(['status' => 'debug', 'message' => 'Datos recibidos correctamente en auth.php']);
    exit;

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (!$username || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario y contraseña son obligatorios.']);
        exit;
    }

    // Consulta a la base de datos para verificar usuario
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrectos.']);
        exit;
    }

    $user = $result->fetch_assoc();

    // Verificar contraseña
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrectos.']);
        exit;
    }

    // Iniciar sesión para el usuario
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['id_rol'] = $user['id_rol'];

    echo json_encode(['status' => 'success', 'message' => 'Inicio de sesión exitoso.']);
    exit;
}

// Si no es POST, redirigir al formulario de inicio de sesión
header('Location: ../templates/login.html');
exit;
?>