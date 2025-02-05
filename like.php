<?php
// like.php
require_once 'includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
    $user_id = $_SESSION['user_id'];

    // Verificar si ya existe like
    $sql = "SELECT id FROM likes WHERE user_id = $user_id AND post_id = $post_id";
    $result = $mysqli->query($sql);
    if ($result->num_rows == 0) {
        $sql = "INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)";
        $mysqli->query($sql);
    }
}
header("Location: index.php");
exit;
?>
