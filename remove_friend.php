<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_POST['friendship_id'])) {
    header('Location: friends.php?error=invalid_request');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "linkfast_db");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$friendship_id = intval($_POST['friendship_id']);
$user_id = $_SESSION['user_id'];
$current_time = date('Y-m-d H:i:s');

try {
    $mysqli->begin_transaction();

    // Verificar que la solicitud de amistad existe y pertenece al usuario actual
    $check_query = "SELECT * FROM friend_requests 
                   WHERE id = ? 
                   AND (sender_id = ? OR receiver_id = ?)
                   AND status = 'accepted'";
    
    $stmt = $mysqli->prepare($check_query);
    $stmt->bind_param("iii", $friendship_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Friendship not found or unauthorized");
    }

    // Actualizar el estado de la amistad a 'removed' y la fecha de actualización
    $update_query = "UPDATE friend_requests 
                    SET status = 'removed', 
                        updated_at = ? 
                    WHERE id = ?";
    
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("si", $current_time, $friendship_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error removing friendship");
    }

    $mysqli->commit();
    header('Location: friends.php?success=friend_removed');
    exit();

} catch (Exception $e) {
    $mysqli->rollback();
    header('Location: friends.php?error=remove_failed');
    exit();
} finally {
    $mysqli->close();
}
?>