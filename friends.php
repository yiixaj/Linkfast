<?php
require_once 'includes/init.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener solicitudes pendientes
$pending_sql = $mysqli->prepare("
    SELECT 
        fr.id as request_id,
        u.id as user_id,
        u.username,
        u.profile_pic,
        fr.sent_at
    FROM friend_requests fr
    JOIN users u ON fr.sender_id = u.id
    WHERE fr.receiver_id = ? 
    AND fr.status = 'pending'
    ORDER BY fr.sent_at DESC
");

$pending_sql->bind_param("i", $user_id);
$pending_sql->execute();
$pending_result = $pending_sql->get_result();

// Obtener lista de amigos
$friends_sql = $mysqli->prepare("
    SELECT 
        u.id,
        u.username,
        u.profile_pic,
        fr.id as friendship_id
    FROM users u
    JOIN friend_requests fr ON 
        ((fr.sender_id = u.id AND fr.receiver_id = ?) 
        OR (fr.receiver_id = u.id AND fr.sender_id = ?))
        AND fr.status = 'accepted'
    WHERE u.id != ?
    ORDER BY u.username ASC
");

$friends_sql->bind_param("iii", $user_id, $user_id, $user_id);
$friends_sql->execute();
$friends_result = $friends_sql->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Amigos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    
 <style>
    :root {
    --primary-color: #552FFA;
    --secondary-color: #A084D7;
    --accent-color: #D048BC;
    --highlight-color: #71BDF1;
    --background-color: #F2F2F2;
    --text-dark: #2A2A2A;
    --text-muted: #6c757d;
}

body {
    background: var(--background-color);
    font-family: 'Roboto', sans-serif;
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
}

.nav-link:hover {
    color: var(--highlight-color) !important;
}

/* Layout principal */
.main-container {
    display: flex;
    gap: 20px;
    padding: 20px;
    margin-top: 70px;
}

/* Sidebar mejorado */
.sidebar {
    width: 250px;
    min-height: 100vh;
    background: white;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    border-radius: 10px;
}

/* Estilos mejorados para enlaces y títulos del sidebar */
.sidebar h5, 
.sidebar .sidebar-heading,
.sidebar .card-title {
    color: var(--text-dark);
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.1rem;
    font-family: 'Roboto', sans-serif;
}

.sidebar .nav-link {
    color: var(--text-dark) !important;
    padding: 0.8rem 1rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
    font-family: 'Roboto', sans-serif;
    font-size: 1rem;
    font-weight: 500;
}

.sidebar .nav-link:hover {
    color: var(--primary-color) !important;
    background-color: rgba(85, 47, 250, 0.1);
    transform: translateX(5px);
}

.sidebar .nav-link.active {
    background-color: var(--primary-color);
    color: white !important;
}

/* Agregar iconos con estilo consistente */
.sidebar .nav-link i {
    margin-right: 10px;
    font-size: 1.1rem;
}

/* Responsive sidebar */
@media (max-width: 992px) {
    .sidebar {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 250px;
        z-index: 1000;
    }
    .sidebar.open {
        display: block;
    }
    .toggle-sidebar {
        display: block;
        margin-bottom: 10px;
    }
}

/* Contenido principal */
.content {
    flex: 1;
}

.card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    padding: 20px;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}

/* Botones */
.btn-primary, .btn-danger {
    border: none;
    transition: all 0.3s ease;
    font-family: 'Roboto', sans-serif;
}

.btn-primary {
    background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
}

.btn-primary:hover {
    background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(85, 47, 250, 0.3);
}

.btn-danger {
    background: linear-gradient(45deg, #ff4b5c, #ff6b6b);
}

.btn-danger:hover {
    background: linear-gradient(45deg, #ff6b6b, #ff4b5c);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(255, 75, 92, 0.3);
}

/* Avatar */
.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--primary-color);
}

/* Mejoras para la lista de amigos */
.friends-list .friend-item {
    transition: all 0.3s ease;
    font-family: 'Roboto', sans-serif;
}

.friends-list .friend-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.friends-list .friend-item .text-primary {
    font-weight: 500;
}

/* Agregar al CSS existente */
.pending-requests {
    margin-bottom: 2rem;
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(45deg, #20c997, #28a745);
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.gap-2 {
    gap: 0.5rem;
}

/* Animación para nuevas solicitudes */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.pending-requests .friend-item {
    animation: fadeIn 0.3s ease-out forwards;
}


</style>

</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include 'includes/sidebar.php'; ?>
    </div>

    <!-- Contenido principal -->
    <div class="content">
        <!-- Sección de Solicitudes Pendientes -->
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="text-primary mb-4">Solicitudes de Amistad Pendientes</h3>
                
                <?php if ($pending_result->num_rows > 0): ?>
                    <div class="pending-requests">
                        <?php while ($request = $pending_result->fetch_assoc()): ?>
                            <div class="friend-item d-flex align-items-center p-3 bg-white rounded shadow-sm mb-2">
                                <img src="<?php echo !empty($request['profile_pic']) ? htmlspecialchars($request['profile_pic']) : 'https://via.placeholder.com/50'; ?>" 
                                     alt="Foto de perfil" 
                                     class="avatar">
                                     
                                <div class="ms-3 flex-grow-1">
                                    <span class="fw-bold text-primary"><?php echo htmlspecialchars($request['username']); ?></span>
                                    <br>
                                    <small class="text-muted">Enviada el <?php echo date('d/m/Y H:i', strtotime($request['sent_at'])); ?></small>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <form action="accept_request.php" method="POST" class="d-inline">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-lg"></i> Aceptar
                                        </button>
                                    </form>
                                    
                                    <form action="reject_request.php" method="POST" class="d-inline">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-x-lg"></i> Rechazar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">No tienes solicitudes de amistad pendientes</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sección de Amigos -->
        <div class="card">
            <div class="card-body">
                <h3 class="text-primary mb-4">Mis Amigos</h3>
                
                <?php if ($friends_result->num_rows > 0): ?>
                    <div class="friends-list">
                        <?php while ($friend = $friends_result->fetch_assoc()): ?>
                            <div class="friend-item d-flex align-items-center p-3 bg-white rounded shadow-sm mb-2">
                                <img src="<?php echo !empty($friend['profile_pic']) ? htmlspecialchars($friend['profile_pic']) : 'https://via.placeholder.com/50'; ?>" 
                                     alt="Foto de perfil" 
                                     class="avatar">
                                     
                                <span class="ms-3 flex-grow-1 fw-bold text-primary">
                                    <?php echo htmlspecialchars($friend['username']); ?>
                                </span>
                                
                                <div class="d-flex gap-2">
                                    <a href="profile.php?id=<?php echo $friend['id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="bi bi-person"></i> Ver Perfil
                                    </a>
                                    
                                    <form action="remove_friend.php" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar a este amigo?');">
                                        <input type="hidden" name="friendship_id" value="<?php echo $friend['friendship_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-person-x"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">Aún no tienes amigos agregados</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mostrar/ocultar sidebar en móviles
    function toggleSidebar() {
        document.querySelector(".sidebar").classList.toggle("open");
    }
</script>

</body>
</html>