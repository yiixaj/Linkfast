// includes/get_messages.php
<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'] ?? null;
$last_id = $_GET['last_id'] ?? 0;

if (!$receiver_id) {
    echo json_encode(['error' => 'Receptor no especificado']);
    exit();
}

$query = "SELECT id, sender_id, receiver_id, message, sent_at 
          FROM messages 
          WHERE ((sender_id = ? AND receiver_id = ?) 
             OR (sender_id = ? AND receiver_id = ?))
             AND id > ?
          ORDER BY sent_at ASC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('iiiii', $sender_id, $receiver_id, $receiver_id, $sender_id, $last_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode(['messages' => $messages]);