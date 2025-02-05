<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Verificar que el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar que la petición es POST y es JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Obtener y decodificar el JSON del body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['receiver_id']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = (int)$data['receiver_id'];
$message = trim($data['message']);

// Validar el mensaje
if (empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'El mensaje no puede estar vacío']);
    exit();
}

// Verificar que los usuarios son amigos
function areUsersFriends($mysqli, $user_id, $receiver_id) {
    $query = "SELECT COUNT(*) as count FROM friend_requests 
              WHERE ((sender_id = ? AND receiver_id = ?) 
              OR (receiver_id = ? AND sender_id = ?))
              AND status = 'accepted'";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iiii', $user_id, $receiver_id, $user_id, $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

if (!areUsersFriends($mysqli, $sender_id, $receiver_id)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No puedes enviar mensajes a este usuario']);
    exit();
}

// Insertar el mensaje en la base de datos
$query = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())";
$stmt = $mysqli->prepare($query);

if (!$stmt) {
    error_log("Error preparing statement: " . $mysqli->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje']);
    exit();
}

$stmt->bind_param('iis', $sender_id, $receiver_id, $message);

if (!$stmt->execute()) {
    error_log("Error executing statement: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje']);
    exit();
}

// Obtener el ID del mensaje insertado
$message_id = $stmt->insert_id;

// Devolver respuesta exitosa
echo json_encode([
    'success' => true,
    'message_id' => $message_id,
    'sent_at' => date('Y-m-d H:i:s')
]);