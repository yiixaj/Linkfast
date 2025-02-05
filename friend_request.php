<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$receiver_id = $_SESSION['user_id'];

// Consulta mejorada
$sql = "SELECT 
            fr.id as request_id,
            fr.sender_id,
            fr.sent_at,
            u.username,
            u.profile_pic
        FROM friend_requests fr
        JOIN users u ON fr.sender_id = u.id 
        WHERE fr.receiver_id = ? 
        AND fr.status = 'pending'
        ORDER BY fr.sent_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$receiver_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Solo para debugging - eliminar en producción
// echo "ID del receptor: " . $receiver_id . "<br>";
// echo "Solicitudes recuperadas: ";
// print_r($requests);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Solicitudes de Amistad</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>
    
    <div class="container">
        <h1>Solicitudes de Amistad</h1>
        
        <?php if (count($requests) > 0): ?>
            <div class="friend-requests-container">
                <?php foreach ($requests as $request): ?>
                    <div class="friend-request-card">
                        <div class="user-info">
                            <img src="<?php echo !empty($request['profile_pic']) ? 'uploads/' . htmlspecialchars($request['profile_pic']) : 'images/default-profile.png'; ?>" 
                                 alt="Foto de perfil" 
                                 class="profile-pic">
                            <div class="user-details">
                                <h3><?php echo htmlspecialchars($request['username']); ?></h3>
                                <span class="time-ago"><?php echo date('d/m/Y H:i', strtotime($request['sent_at'])); ?></span>
                            </div>
                        </div>
                        <div class="action-buttons">
                            <form action="accept_request.php" method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                <button type="submit" class="btn accept">Aceptar</button>
                            </form>
                            <form action="reject_request.php" method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                <button type="submit" class="btn reject">Rechazar</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-requests">
                <p>No tienes solicitudes de amistad pendientes.</p>
                <a href="index.php" class="btn">Volver al inicio</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>