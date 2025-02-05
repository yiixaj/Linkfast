<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT 
            u.id, 
            u.username, 
            u.profile_pic,
            m.message AS last_message,
            m.sent_at AS last_message_time
          FROM users u
          INNER JOIN friend_requests fr ON 
            (fr.sender_id = u.id AND fr.receiver_id = ?) 
            OR (fr.receiver_id = u.id AND fr.sender_id = ?)
          LEFT JOIN (
              SELECT 
                  sender_id,
                  receiver_id,
                  message,
                  sent_at,
                  CASE 
                      WHEN sender_id = ? THEN receiver_id 
                      ELSE sender_id 
                  END AS other_user
              FROM messages
              WHERE sender_id = ? OR receiver_id = ?
          ) m ON u.id = m.other_user
          WHERE u.id != ?
            AND fr.status = 'accepted'
          ORDER BY m.sent_at DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('iiiiiii', 
    $user_id, // fr.receiver_id (primer ?)
    $user_id, // fr.sender_id (segundo ?)
    $user_id, // CASE WHEN sender_id
    $user_id, // WHERE sender_id
    $user_id, // WHERE receiver_id
    $user_id, // u.id != ?
    $user_id  // Aseguramos coherencia en JOIN
);
$conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'success' => true, 
    'conversations' => $conversations
]);