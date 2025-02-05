<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Debes iniciar sesión.");
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];

try {
    // Verificar solicitudes activas existentes
    $check_query = "SELECT id, status 
                    FROM friend_requests 
                    WHERE ((sender_id = ? AND receiver_id = ?) 
                          OR (sender_id = ? AND receiver_id = ?))
                    AND status IN ('pending', 'accepted')
                    LIMIT 1";
    
    $stmt = $mysqli->prepare($check_query);
    $stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['status'] === 'accepted') {
            header("Location: profile.php?id=$receiver_id&message=already_friends");
            exit();
        } elseif ($row['status'] === 'pending') {
            header("Location: profile.php?id=$receiver_id&message=request_pending");
            exit();
        }
    }
    
    // Marcar solicitudes anteriores como inactivas
    $update_query = "UPDATE friend_requests 
                     SET status = 'inactive', 
                         updated_at = NOW() 
                     WHERE (sender_id = ? AND receiver_id = ?) 
                     OR (sender_id = ? AND receiver_id = ?)";
    
    $stmt = $mysqli->prepare($update_query);
    $stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
    $stmt->execute();
    
    // Crear nueva solicitud
    $insert_query = "INSERT INTO friend_requests 
                     (sender_id, receiver_id, status, sent_at, created_at, updated_at) 
                     VALUES (?, ?, 'pending', NOW(), NOW(), NOW())";
    
    $stmt = $mysqli->prepare($insert_query);
    $stmt->bind_param("ii", $sender_id, $receiver_id);
    
    if ($stmt->execute()) {
        header("Location: profile.php?id=$receiver_id&message=request_sent");
        exit();
    } else {
        error_log("Error al crear solicitud: " . $mysqli->error);
        header("Location: profile.php?id=$receiver_id&error=failed_to_send");
        exit();
    }

} catch (Exception $e) {
    error_log("Error en send_request.php: " . $e->getMessage());
    header("Location: profile.php?id=$receiver_id&error=system_error");
    exit();
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
?>