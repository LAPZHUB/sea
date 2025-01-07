<?php
// Incluir conexión a la base de datos
require_once 'db.php';

// Iniciar sesión si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
    exit;
}

// Función para sanitizar entradas
function sanitize_input($data) {
    return htmlspecialchars(strip_tags($data));
}

// CRUD para "dirigente"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_dirigente'])) {
    $nombre = sanitize_input($_POST['nom_dirigente']);
    $usuario = sanitize_input($_POST['usuario']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = $conn->prepare("INSERT INTO dirigente (nom_dirigente, usuario, password) VALUES (:nombre, :usuario, :password)");
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':usuario', $usuario);
    $query->bindParam(':password', $password);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Dirigente creado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear dirigente']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id_lider'])) {
    $query = $conn->query("SELECT * FROM dirigente");
    $dirigentes = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($dirigentes);
    exit;
}

// CRUD para "lider"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_lider'])) {
    $id_dirigente = filter_input(INPUT_POST, 'id_dirigente', FILTER_VALIDATE_INT);
    $nombre = sanitize_input($_POST['nom_lider']);
    $usuario = sanitize_input($_POST['usuario']);
    $estado = sanitize_input($_POST['estado_lider']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = $conn->prepare("INSERT INTO lider (id_dirigente, nom_lider, usuario, estado_lider, password) VALUES (:id_dirigente, :nombre, :usuario, :estado, :password)");
    $query->bindParam(':id_dirigente', $id_dirigente);
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':usuario', $usuario);
    $query->bindParam(':estado', $estado);
    $query->bindParam(':password', $password);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Líder creado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear líder']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_dirigente'])) {
    $id_dirigente = filter_input(INPUT_GET, 'id_dirigente', FILTER_VALIDATE_INT);

    $query = $conn->prepare("SELECT * FROM lider WHERE id_dirigente = :id_dirigente");
    $query->bindParam(':id_dirigente', $id_dirigente);
    $query->execute();
    $lideres = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($lideres);
    exit;
}

// CRUD para "capturista"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_capturista'])) {
    $id_dirigente = filter_input(INPUT_POST, 'id_dirigente', FILTER_VALIDATE_INT);
    $id_lider = filter_input(INPUT_POST, 'id_lider', FILTER_VALIDATE_INT);
    $nombre = sanitize_input($_POST['nom_capturista']);
    $usuario = sanitize_input($_POST['usuario']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = $conn->prepare("INSERT INTO capturista (id_dirigente, id_lider, nom_capturista, usuario, password) VALUES (:id_dirigente, :id_lider, :nombre, :usuario, :password)");
    $query->bindParam(':id_dirigente', $id_dirigente);
    $query->bindParam(':id_lider', $id_lider);
    $query->bindParam(':nombre', $nombre);
    $query->bindParam(':usuario', $usuario);
    $query->bindParam(':password', $password);

    if ($query->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Capturista creado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear capturista']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_lider'])) {
    $id_lider = filter_input(INPUT_GET, 'id_lider', FILTER_VALIDATE_INT);

    $query = $conn->prepare("SELECT * FROM capturista WHERE id_lider = :id_lider");
    $query->bindParam(':id_lider', $id_lider);
    $query->execute();
    $capturistas = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($capturistas);
    exit;
}
?>
