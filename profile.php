<?php
require_once 'includes/init.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Procesar la subida de imágenes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['user_id'] == $_GET['id']) {
    $upload_dir = 'uploads/';
    
    // Procesar avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_info = pathinfo($_FILES['avatar']['name']);
        $avatar_filename = uniqid() . '.' . $file_info['extension'];
        $avatar_path = $upload_dir . $avatar_filename;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
            $sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $avatar_path, $_SESSION['user_id']);
            $stmt->execute();
        }
    }
    
    // Procesar banner
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
        $file_info = pathinfo($_FILES['banner']['name']);
        $banner_filename = uniqid() . '.' . $file_info['extension'];
        $banner_path = $upload_dir . $banner_filename;
        
        if (move_uploaded_file($_FILES['banner']['tmp_name'], $banner_path)) {
            // Añadir columna banner_pic si no existe
            $mysqli->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS banner_pic VARCHAR(255)");
            
            $sql = "UPDATE users SET banner_pic = ? WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("si", $banner_path, $_SESSION['user_id']);
            $stmt->execute();
        }
    }
    
    // Redireccionar para evitar reenvío del formulario
    header("Location: profile.php?id=" . $_GET['id']);
    exit;
}

// Obtener el ID del perfil desde la URL
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['user_id'];

// Obtener información del perfil
$sql = "SELECT id, username, email, profile_pic, banner_pic, created_at FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró el perfil
if ($result->num_rows === 0) {
    die("Perfil no encontrado.");
}

$profile = $result->fetch_assoc();

// Obtener las publicaciones del usuario
$posts = [];
$sql_posts = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $mysqli->prepare($sql_posts);
$stmt->bind_param("i", $profile_id);
$stmt->execute();
$result_posts = $stmt->get_result();

while ($row = $result_posts->fetch_assoc()) {
    $posts[] = $row;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?php echo htmlspecialchars($profile['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
    --primary-color: #552FFA;
    --secondary-color: #A084D7;
    --accent-color: #D048BC;
    --highlight-color: #71BDF1;
    --background-color: #F2F2F2;
    --text-primary: #333333;
    --text-secondary: #666666;
    --success-color: #28a745;
    --error-color: #dc3545;
}

/* Estilos generales */
body {
    background: var(--background-color);
    font-family: 'Roboto', sans-serif;
    padding-top: 56px;
    color: var(--text-primary);
}

/* Navbar */
.navbar {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
    box-shadow: 0 4px 15px rgba(85, 47, 250, 0.3);
}

.navbar-brand {
    font-weight: bold;
    color: white !important;
}

.nav-link {
    color: white !important;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 8px;
}

.nav-link:hover {
    color: var(--highlight-color) !important;
    background-color: rgba(255, 255, 255, 0.1);
}

/* Contenedor principal */
.profile-container {
    max-width: 851px;  /* Ajustado para coincidir con el banner */
    margin: 20px auto;
    padding: 20px;
}

/* Banner */
.profile-banner {
    height: 315px;
    width: 100%;
    max-width: 851px;
    background-size: cover;
    background-position: center;
    position: relative;
    border-radius: 16px 16px 0 0;
    margin-bottom: -60px;
    transition: all 0.3s ease;
}


.banner-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100%;
    background: linear-gradient(to bottom, transparent 50%, rgba(0,0,0,0.7));
    border-radius: 16px 16px 0 0;
}

/* Encabezado del perfil */
.profile-header {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    text-align: center;
    position: relative;
    margin-bottom: 2rem;
    transition: transform 0.3s ease;
}

.profile-header:hover {
    transform: translateY(-5px);
}

/* Avatar */
.avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 1rem;
    border: 4px solid white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.avatar:hover {
    transform: scale(1.05);
}

/* Información del usuario */
.username {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.joined {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

/* Botones */
.edit-profile-button {
    position: absolute;
    top: 20px;
    right: 20px;
    background: var(--primary-color);
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.edit-profile-button:hover {
    background: var(--accent-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(85, 47, 250, 0.3);
}

.btn-primary {
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
    border: none;
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(85, 47, 250, 0.3);
}

/* Posts container - Añade estas nuevas clases */
.profile-posts {
    margin-top: 2rem;
    max-width: 600px;  /* Mismo ancho que main-content en index.php */
    margin-left: auto;
    margin-right: auto;
}
.post-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 100%;  /* Asegura que ocupe todo el ancho del contenedor */
}

.post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}

.post-content {
    font-size: 1.1rem;
    line-height: 1.6;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.post-date {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

/* Modal de edición */
.image-upload-modal .modal-content {
    border-radius: 16px;
    border: none;
}

.image-upload-modal .modal-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
    color: white;
    border-radius: 16px 16px 0 0;
}

.image-upload-modal .preview-image {
    max-width: 100%;
    max-height: 200px;
    margin: 10px 0;
    border-radius: 8px;
    overflow: hidden;
}

.image-upload-modal .preview-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

/* Inputs */
.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(85, 47, 250, 0.25);
}

/* Responsive */
@media (max-width: 768px) {
    .profile-container {
        padding: 10px;
    }

    .profile-banner {
        height: 200px;
        margin-bottom: -40px;
    }

    .avatar {
        width: 100px;
        height: 100px;
    }

    .profile-header {
        padding: 1.5rem;
    }

    .edit-profile-button {
        top: 10px;
        right: 10px;
    }
}

@media (max-width: 576px) {
    .profile-banner {
        height: 150px;
    }

    .username {
        font-size: 1.2rem;
    }

    .post-card {
        padding: 1rem;
    }


    /* Ajusta el responsive */
    @media (max-width: 768px) {
    .profile-posts {
        max-width: 100%;
        padding: 0 15px;
    }
}
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="profile-container">
    <!-- Banner -->
    <div class="profile-banner" style="background-image: url('<?php echo !empty($profile['banner_pic']) ? htmlspecialchars($profile['banner_pic']) : "https://via.placeholder.com/800x300"; ?>')">
        <div class="banner-overlay"></div>
    </div>

    <!-- Encabezado del perfil -->
    <div class="profile-header">
        <?php if ($profile_id == $_SESSION['user_id']): ?>
            <button type="button" class="btn btn-primary edit-profile-button" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="bi bi-pencil"></i> Editar perfil
            </button>
        <?php endif; ?>

        <img src="<?php echo !empty($profile['profile_pic']) ? htmlspecialchars($profile['profile_pic']) : 'https://via.placeholder.com/120'; ?>" alt="Avatar" class="avatar">
        <div class="username"><?php echo htmlspecialchars($profile['username']); ?></div>
        <div class="joined">Se unió el <?php echo date('d/m/Y', strtotime($profile['created_at'])); ?></div>

     <!-- Verificar y mostrar botón de solicitud de amistad -->
<?php if ($profile_id != $_SESSION['user_id']): ?>
    <?php
// Verificar si hay una solicitud de amistad pendiente o activa
$checkRequest = $mysqli->prepare("
    SELECT id, status, updated_at 
    FROM friend_requests 
    WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
    AND (status = 'pending' OR status = 'accepted')
    ORDER BY updated_at DESC 
    LIMIT 1
");
$checkRequest->bind_param("iiii", $_SESSION['user_id'], $profile_id, $profile_id, $_SESSION['user_id']);
$checkRequest->execute();
$resultRequest = $checkRequest->get_result();
$friendshipStatus = $resultRequest->fetch_assoc();

if (!$friendshipStatus): ?>
    <!-- No hay solicitud activa o pendiente, mostrar botón para enviar solicitud -->
    <form action="send_request.php" method="POST">
        <input type="hidden" name="receiver_id" value="<?php echo $profile_id; ?>">
        <button type="submit" class="btn btn-success">Enviar solicitud de amistad</button>
    </form>
<?php elseif ($friendshipStatus['status'] == 'pending'): ?>
    <!-- Solicitud pendiente -->
    <button class="btn btn-warning" disabled>Solicitud enviada</button>
<?php elseif ($friendshipStatus['status'] == 'accepted'): ?>
    <!-- Ya son amigos -->
    <button class="btn btn-secondary" disabled>Ya son amigos</button>
<?php endif; ?>
<?php endif; ?>   
    </div>
</div>




        <!-- Modal para editar perfil -->
        <?php if ($profile_id == $_SESSION['user_id']): ?>
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Editar perfil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="profile.php?id=<?php echo $_SESSION['user_id']; ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">Foto de perfil</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                <div id="avatarPreview" class="preview-image"></div>
                            </div>
                            <div class="mb-3">
                                <label for="banner" class="form-label">Imagen de banner</label>
                                <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
                                <div id="bannerPreview" class="preview-image"></div>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Publicaciones del usuario -->
        <div class="profile-posts">
            <h3 class="text-center mb-4" style="color: var(--primary-color);">Publicaciones</h3>
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post image" class="img-fluid mt-2 rounded">
                        <?php endif; ?>
                        <div class="post-date"><?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center" style="color: var(--secondary-color);">No hay publicaciones.</p>
            <?php endif; ?>
        </div>
    </div>
    </div>

    <!-- Bootstrap Bundle JS con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para previsualización de imágenes -->
    <script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid">`;
            }
            reader.readAsDataURL(file);
        }
    }

    document.getElementById('avatar').addEventListener('change', function() {
        previewImage(this, 'avatarPreview');
    });

    document.getElementById('banner').addEventListener('change', function() {
        previewImage(this, 'bannerPreview');
    });
    </script>
</body>
</html>