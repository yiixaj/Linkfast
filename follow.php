<?php
// follow.php
require_once 'includes/init.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar que se haya pasado el parámetro user_id por GET
if (!isset($_GET['user_id'])) {
    header("Location: index.php");
    exit;
}

$follower_id = $_SESSION['user_id'];
$following_id = intval($_GET['user_id']);

// Evitar que el usuario se siga a sí mismo
if ($follower_id === $following_id) {
    header("Location: index.php");
    exit;
}

// Verificar si ya existe la relación de seguimiento
$stmt = $mysqli->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
$stmt->bind_param("ii", $follower_id, $following_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Ya se está siguiendo a este usuario
    header("Location: index.php");
    exit;
}

// Insertar la relación de seguimiento en la base de datos
$stmt = $mysqli->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
$stmt->bind_param("ii", $follower_id, $following_id);

if ($stmt->execute()) {
    // Redirigir a la página principal u otra de tu elección
    header("Location: index.php");
    exit;
} else {
    echo "Error al seguir al usuario: " . $stmt->error;
}
?>
