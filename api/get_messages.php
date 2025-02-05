<?php
// api/get_messages.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : null;
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

if (!$receiver_id) {
    echo json_encode(['success' => false, 'error' => 'Receptor no especificado']);
    exit();
}

// Verificar si son amigos
$friend_check = $mysqli->prepare("SELECT COUNT(*) as count FROM friend_requests 
    WHERE ((sender_id = ? AND receiver_id = ?) OR (receiver_id = ? AND sender_id = ?))
    AND status = 'accepted'");
$friend_check->bind_param('iiii', $user_id, $receiver_id, $user_id, $receiver_id);
$friend_check->execute();
$result = $friend_check->get_result()->fetch_assoc();

if ($result['count'] == 0) {
    echo json_encode(['success' => false, 'error' => 'No autorizado para ver estos mensajes']);
    exit();
}

// Obtener nuevos mensajes
$query = "SELECT m.*, u.username, u.profile_pic
          FROM messages m 
          INNER JOIN users u ON m.sender_id = u.id
          WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
             OR (m.sender_id = ? AND m.receiver_id = ?))
             AND m.id > ?
          ORDER BY m.sent_at ASC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('iiiii', $user_id, $receiver_id, $receiver_id, $user_id, $last_id);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success' => true,
    'messages' => $messages
]);