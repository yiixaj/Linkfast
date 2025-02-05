<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "linkfast_db";

// Crear conexión
$mysqli = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}
?>
