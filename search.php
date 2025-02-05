<?php
require_once 'includes/init.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$results = [];
if (isset($_GET['query'])) {
    $query = $mysqli->real_escape_string(trim($_GET['query']));
    $sql = "SELECT id, username, email, profile_pic FROM users WHERE username LIKE '%$query%' OR email LIKE '%$query%'";
    $result = $mysqli->query($sql);
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar perfiles</title>
    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #552FFA;
            --secondary-color: #A084D7;
            --accent-color: #D048BC;
            --highlight-color: #71BDF1;
            --background-color: #F2F2F2;
        }

        body {
            background: var(--background-color);
            font-family: 'Roboto', sans-serif;
        }

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

        .search-container {
            max-width: 800px;
            margin: 80px auto 20px;
            padding: 20px;
        }

        .search-form {
            margin-bottom: 2rem;
        }

        .search-form .form-control {
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            padding: 10px;
            font-size: 1rem;
        }

        .search-form .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .search-form .btn-primary:hover {
            background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(85, 47, 250, 0.3);
        }

        .user-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }

        .user-card .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
        }

        .user-card .username {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .user-card .email {
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .user-card .action-btn {
            margin-left: auto;
        }

        .user-card .action-btn .btn {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>
    <!-- Contenido principal -->
    <div class="search-container">
        <h2 class="text-center mb-4" style="color: var(--primary-color);">Buscar perfiles</h2>
        <form method="get" action="search.php" class="search-form d-flex gap-2">
            <input type="text" name="query" class="form-control" placeholder="Buscar usuarios..." required>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
        <hr>

        <!-- Resultados de búsqueda -->
        <?php if (!empty($results)): ?>
            <div class="results">
                <?php foreach ($results as $user): ?>
                    <div class="user-card d-flex align-items-center">
                        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Avatar" class="avatar">
                        <div>
                            <div class="username"><?php echo htmlspecialchars($user['username']); ?></div>
                            
                        </div>
                        <div class="action-btn">
                            <a href="profile.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Ver perfil</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center" style="color: var(--secondary-color);">No se encontraron resultados.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap Bundle JS con Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>