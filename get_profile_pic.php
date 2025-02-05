<?php
// Verificar si se ha proporcionado el parámetro 'pic'
if (isset($_GET['pic'])) {
    $profilePic = $_GET['pic'];

    // Ruta de la carpeta donde se almacenan las imágenes de perfil
    $uploadDir = 'uploads/';
    $imagePath = $uploadDir . $profilePic;

    // Verificar si el archivo existe
    if (file_exists($imagePath)) {
        // Establecer los encabezados correctos para la imagen
        header('Content-Type: image/jpeg');

        // Leer y enviar el contenido de la imagen
        readfile($imagePath);
        exit;
    } else {
        // Si el archivo no existe, usar la imagen por defecto
        $defaultImagePath = 'images/default-avatar.svg';
        if (file_exists($defaultImagePath)) {
            header('Content-Type: image/svg+xml');
            readfile($defaultImagePath);
            exit;
        } else {
            // Si no hay imagen por defecto, generar una imagen placeholder
            $image = imagecreate(200, 200);
            $backgroundColor = imagecolorallocate($image, 230, 230, 230);
            $textColor = imagecolorallocate($image, 150, 150, 150);
            imagestring($image, 5, 50, 80, 'Imagen no disponible', $textColor);
            header('Content-Type: image/png');
            imagepng($image);
            imagedestroy($image);
            exit;
        }
    }
} else {
    // Si no se ha proporcionado el parámetro 'pic', redirigir a una página de error
    http_response_code(404);
    echo 'Parámetro de imagen no proporcionado.';
}
?>