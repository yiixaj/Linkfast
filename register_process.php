<?php
session_start();
require_once 'includes/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $username = $mysqli->real_escape_string(trim($_POST['username']));
    $email = $mysqli->real_escape_string(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar que todos los campos estén completos
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Por favor completa todos los campos.";
        header("Location: register.php");
        exit;
    }

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: register.php");
        exit;
    }

    // Validar la contraseña
    if (strlen($password) < 8) {
        $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
        header("Location: register.php");
        exit;
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $_SESSION['error'] = "La contraseña debe contener al menos una letra mayúscula.";
        header("Location: register.php");
        exit;
    }

    if (!preg_match('/[a-z]/', $password)) {
        $_SESSION['error'] = "La contraseña debe contener al menos una letra minúscula.";
        header("Location: register.php");
        exit;
    }

    if (!preg_match('/[0-9]/', $password)) {
        $_SESSION['error'] = "La contraseña debe contener al menos un número.";
        header("Location: register.php");
        exit;
    }

    // Si todo está bien, proceder con el registro
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$passwordHash')";
    if ($mysqli->query($sql)) {
        header("Location: login.php?register=success");
        exit;
    } else {
        $_SESSION['error'] = "Error en el registro: " . $mysqli->error;
        header("Location: register.php");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>