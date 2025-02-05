<?php
// comment_process.php
require_once 'includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = intval($_POST['post_id']);
    $comment = $mysqli->real_escape_string(trim($_POST['comment']));
    $user_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        $sql = "INSERT INTO comments (user_id, post_id, comment) VALUES ($user_id, $post_id, '$comment')";
        $mysqli->query($sql);
    }
    header("Location: index.php");
    exit;
}
?>
