<?php
require_once 'includes/init.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];
    
    // Verificar si ya existe el like
    $check_sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $post_id);
    $check_stmt->execute();
    $existing_like = $check_stmt->get_result()->fetch_assoc();
    
    if ($existing_like) {
        // Si existe, eliminar el like
        $delete_sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
        $stmt = $mysqli->prepare($delete_sql);
        $stmt->bind_param("ii", $user_id, $post_id);
        $success = $stmt->execute();
        
        if ($success) {
            // Obtener el nuevo conteo de likes
            $count_sql = "SELECT COUNT(*) as count FROM likes WHERE post_id = ?";
            $count_stmt = $mysqli->prepare($count_sql);
            $count_stmt->bind_param("i", $post_id);
            $count_stmt->execute();
            $new_count = $count_stmt->get_result()->fetch_assoc()['count'];
            
            echo json_encode(['status' => 'unliked', 'likes' => $new_count]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al quitar el like']);
        }
    } else {
        // Si no existe, crear el like
        $insert_sql = "INSERT INTO likes (user_id, post_id) VALUES (?, ?)";
        $stmt = $mysqli->prepare($insert_sql);
        $stmt->bind_param("ii", $user_id, $post_id);
        $success = $stmt->execute();
        
        if ($success) {
            // Obtener el nuevo conteo de likes
            $count_sql = "SELECT COUNT(*) as count FROM likes WHERE post_id = ?";
            $count_stmt = $mysqli->prepare($count_sql);
            $count_stmt->bind_param("i", $post_id);
            $count_stmt->execute();
            $new_count = $count_stmt->get_result()->fetch_assoc()['count'];
            
            echo json_encode(['status' => 'liked', 'likes' => $new_count]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al dar like']);
        }
    }
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Solicitud inválida']);