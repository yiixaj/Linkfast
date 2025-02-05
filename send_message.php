<?php
// send_message.php
require_once 'includes/init.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = intval($_POST['receiver_id']);
    $message = $mysqli->real_escape_string(trim($_POST['message']));

    if (!empty($message)) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ($sender_id, $receiver_id, '$message')";
        $mysqli->query($sql);
    }
    // Redireccionar a la conversación (puedes pasar el ID del receptor en la URL)
    header("Location: messages.php?chat_with=$receiver_id");
    exit;
}
?>
