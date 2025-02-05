<?php
// Función para obtener el nombre de usuario por ID
function get_username($user_id) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user ? htmlspecialchars($user['username']) : 'Usuario desconocido';
}

// Función para formatear fechas
function format_date($date) {
    if (!$date) return '';
    return date('H:i', strtotime($date));
}

function get_profile_pic_url($profile_pic) {
    if (empty($profile_pic)) {
        return 'images/default-avatar.svg';
    }
    
    // Como profile_pic ya incluye "uploads/", no necesitamos añadirlo
    if (file_exists($profile_pic)) {  // Simplemente verifica si existe el archivo
        return $profile_pic;  // Retorna la ruta tal como está en la base de datos
    } else {
        return 'images/default-avatar.svg';
    }
}
// Función para verificar si dos usuarios son amigos
function are_friends($user_id1, $user_id2) {
    global $mysqli;
    
    $query = "SELECT COUNT(*) as count FROM friend_requests 
              WHERE ((sender_id = ? AND receiver_id = ?) 
                 OR (sender_id = ? AND receiver_id = ?))
                 AND status = 'accepted'";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iiii', $user_id1, $user_id2, $user_id2, $user_id1);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

// Función para verificar si un usuario existe
function user_exists($user_id) {
    global $mysqli;
    
    $stmt = $mysqli->prepare("SELECT COUNT(*) as count FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

// Función para sanitizar la salida HTML
function html_escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Función para obtener el último mensaje entre dos usuarios
function get_last_message($user1_id, $user2_id) {
    global $mysqli;
    
    $query = "SELECT message, sent_at FROM messages 
              WHERE (sender_id = ? AND receiver_id = ?) 
                 OR (sender_id = ? AND receiver_id = ?) 
              ORDER BY sent_at DESC LIMIT 1";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iiii', $user1_id, $user2_id, $user2_id, $user1_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return [
            'message' => $row['message'],
            'sent_at' => $row['sent_at']
        ];
    }
    
    return null;
}

// Función para verificar si un mensaje pertenece a un usuario
function message_belongs_to_user($message_id, $user_id) {
    global $mysqli;
    
    $query = "SELECT COUNT(*) as count FROM messages 
              WHERE id = ? AND (sender_id = ? OR receiver_id = ?)";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iii', $message_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

