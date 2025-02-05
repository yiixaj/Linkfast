<?php
// login_process.php
require_once 'includes/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = $mysqli->real_escape_string(trim($_POST['identifier']));
    $password = $_POST['password'];

    // Buscar por username o email
    $sql = "SELECT * FROM users WHERE username = '$identifier' OR email = '$identifier' LIMIT 1";
    $result = $mysqli->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verificar contraseña
        if (password_verify($password, $user['password'])) {
            // Establecer sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            die("Contraseña incorrecta.");
        }
    } else {
        die("Usuario no encontrado.");
    }
} else {
    header("Location: login.php");
    exit;
}
?>
