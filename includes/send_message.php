<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Obtener y validar los datos
$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;
$message = trim($_POST['message'] ?? '');

// Validaciones básicas
if (!$receiver_id || empty($message)) {
    echo json_encode(['error' => 'Faltan datos requeridos']);
    exit();
}

// Insertar el mensaje
$query = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) 
          VALUES (?, ?, ?, NOW())";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('iis', $sender_id, $receiver_id, $message);

if ($stmt->execute()) {
    // Obtener el mensaje recién insertado
    $message_id = $stmt->insert_id;
    
    $get_message = "SELECT id, sender_id, receiver_id, message, sent_at 
                    FROM messages WHERE id = ?";
    $stmt = $mysqli->prepare($get_message);
    $stmt->bind_param('i', $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $new_message = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'message' => $new_message
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Error al enviar el mensaje'
    ]);
}