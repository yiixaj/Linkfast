<?php
// post_process.php
require_once 'includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $mysqli->real_escape_string(trim($_POST['content']));
    
    if (empty($content)) {
        die("El post no puede estar vacío.");
    }
    
    $user_id = $_SESSION['user_id'];
    $image_path = null;
    
    // Procesar imagen si se sube
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath   = $_FILES['image']['tmp_name'];
        $fileName      = $_FILES['image']['name'];
        $fileNameCmps  = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Extensiones permitidas
        $allowedfileExtensions = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = 'images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            // Generar nombre único
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_path = $mysqli->real_escape_string($dest_path);
            } else {
                die("Error al mover el archivo subido.");
            }
        } else {
            die("Tipo de archivo no permitido. Solo se permiten imágenes (jpg, jpeg, png, gif).");
        }
    }
    
    // Insertar el post con imagen (si existe)
    if ($image_path) {
        $sql = "INSERT INTO posts (user_id, content, image) VALUES ($user_id, '$content', '$image_path')";
    } else {
        $sql = "INSERT INTO posts (user_id, content) VALUES ($user_id, '$content')";
    }
    
    if ($mysqli->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        die("Error al publicar: " . $mysqli->error);
    }
} else {
    header("Location: index.php");
    exit;
}
?>
