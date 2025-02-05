<?php
// accept_request.php
require_once 'includes/init.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['request_id'])) {
    header("Location: friends.php");
    exit;
}

$request_id = intval($_POST['request_id']);
$user_id = $_SESSION['user_id'];

// Verificar que la solicitud existe y está dirigida al usuario actual
$check_sql = "
    SELECT * FROM friend_requests 
    WHERE id = ? AND receiver_id = ? AND status = 'pending'
";
$stmt = $mysqli->prepare($check_sql);
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Actualizar el estado de la solicitud a 'accepted'
    $update_sql = "UPDATE friend_requests SET status = 'accepted', updated_at = NOW() WHERE id = ?";
    $stmt = $mysqli->prepare($update_sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
}

header("Location: friends.php");
exit;