<?php
// index.php
require_once 'includes/init.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Linkfast</title>
  <!-- Bootstrap CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      margin-bottom: 20px;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
    }

    .card-header {
      background: white;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      padding: 1rem;
    }

    .card-footer {
      background: white;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
      padding: 1rem;
    }

    .btn-primary {
      background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
      border: none;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(85, 47, 250, 0.3);
    }

    .btn-outline-primary {
      border-color: var(--primary-color);
      color: var(--primary-color);
      transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
      background: var(--primary-color);
      color: white;
    }

    .form-control {
      border: 2px solid #e1e1e1;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(85, 47, 250, 0.2);
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      background-color: #f8f9fa;
      border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .post-content {
      font-size: 1.1rem;
      line-height: 1.6;
      padding: 1rem;
    }

    .comment-form {
      margin-top: 1rem;
      padding: 0 1rem 1rem 1rem;
    }

    .comment-input {
      border-radius: 20px;
      padding: 0.5rem 1rem;
    }

    .comment-btn {
      border-radius: 20px;
    }

    /* Layout de tres columnas */
    .main-layout {
      display: flex;
      gap: 20px;
      margin-top: 80px;
      padding: 0 15px;
    }

    .left-sidebar, .right-sidebar {
      width: 250px;
    }

    .main-content {
      flex: 1;
      max-width: 600px;
      margin: 0 auto;
    }

    /* Sección de sugerencias */
    .suggestions-card {
      margin-bottom: 20px;
      position: sticky;
      top: 100px;
    }

    .suggestions-card .card-body {
      padding: 1rem;
    }

    .suggestions-card h5 {
      font-size: 1rem;
      font-weight: bold;
      margin-bottom: 1rem;
      color: var(--primary-color);
    }

    .suggestions-card .user {
      display: flex;
      align-items: center;
      margin-bottom: 0.75rem;
      padding: 0.5rem;
      border-radius: 8px;
      transition: background-color 0.3s ease;
    }

    .suggestions-card .user:hover {
      background-color: rgba(0, 0, 0, 0.03);
    }

    .suggestions-card .user img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
      object-fit: cover;
    }

    .suggestions-card .user .username {
      font-weight: 500;
      flex-grow: 1;
    }

    .suggestions-card .user .action {
      margin-left: auto;
    }

    .suggestions-card .user .action .btn {
      font-size: 0.9rem;
      padding: 0.25rem 0.75rem;
      border-radius: 15px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .left-sidebar {
        display: none;
      }
    }

    @media (max-width: 992px) {
      .right-sidebar {
        display: none;
      }
      
      .main-content {
        max-width: 100%;
      }
    }

    .nav-link.text-dark {
      color: #343a40 !important;
    }

    .nav-link.text-dark:hover {
      background-color: rgba(0, 0, 0, 0.05);
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }
    
    .btn-outline-primary.active {
    background-color: var(--primary-color);
    color: white;
}

.like-button {
    transition: all 0.3s ease;
}

.like-button:hover {
    transform: scale(1.05);
}

.like-button.active:hover {
    background-color: var(--primary-color);
    opacity: 0.9;
}

.like-count {
    margin-left: 5px;
}


  </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

  <!-- Contenido principal -->
  <div class="container main-layout">
    <!-- Sidebar izquierda -->
    <div class="left-sidebar">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Menú</h5>
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link text-dark" href="profile.php?id=<?php echo $_SESSION['user_id']; ?>">
                <i class="bi bi-person"></i> Mi Perfil
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-dark" href="friends.php">
                <i class="bi bi-people"></i> Amigos
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-dark" href="https://www.friv.com/">
                <i class="bi bi-controller"></i> Juegos
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Contenido central (posts) -->
    <div class="main-content">
      <!-- Tarjeta de nuevo post -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">¿Qué estás pensando?</h5>
          <form action="post_process.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <textarea class="form-control" name="content" rows="3" placeholder="Comparte algo..." required></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <input type="file" name="image" class="form-control w-50" accept="image/*">
              <button type="submit" class="btn btn-primary">Publicar</button>
            </div>
          </form>
        </div>
      </div>

      <?php
      // Obtener información del usuario actual (para avatar en comentarios)
      $current_user_sql = "SELECT profile_pic FROM users WHERE id = ?";
      $stmt = $mysqli->prepare($current_user_sql);
      $stmt->bind_param("i", $_SESSION['user_id']);
      $stmt->execute();
      $current_user = $stmt->get_result()->fetch_assoc();

      // Antes de mostrar los posts, reemplaza la consulta actual por esta:
      // Reemplaza la consulta SQL actual por esta:
$sql = "SELECT 
posts.*,
users.username,
users.profile_pic,
COUNT(DISTINCT l.id) as like_count,
MAX(CASE WHEN l.user_id = ? THEN 1 ELSE 0 END) as user_liked
FROM posts 
JOIN users ON posts.user_id = users.id 
LEFT JOIN likes l ON posts.id = l.post_id 
LEFT JOIN friend_requests fr ON (
(fr.sender_id = ? AND fr.receiver_id = posts.user_id) OR 
(fr.receiver_id = ? AND fr.sender_id = posts.user_id)
) AND fr.status = 'accepted'
WHERE posts.user_id = ? OR fr.id IS NOT NULL
GROUP BY posts.id
ORDER BY posts.created_at DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiii", 
$_SESSION['user_id'], 
$_SESSION['user_id'], 
$_SESSION['user_id'], 
$_SESSION['user_id']
);
$stmt->execute();
$result = $stmt->get_result();
      
      while ($post = $result->fetch_assoc()):
      ?>
        <!-- Reemplaza todo el contenido dentro del while que muestra los posts -->
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <img src="<?php echo !empty($post['profile_pic']) ? htmlspecialchars($post['profile_pic']) : 'images/default-avatar.svg'; ?>" 
                   class="avatar me-2" alt="<?php echo htmlspecialchars($post['username']); ?>">
              <div>
                <h6 class="mb-0">
                  <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark">
                    <?php echo htmlspecialchars($post['username']); ?>
                  </a>
                </h6>
                <small class="text-muted">
                  <?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>
                </small>
              </div>
            </div>
          </div>

          <div class="card-body">
            <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <?php if (!empty($post['image'])): ?>
              <img src="<?php echo htmlspecialchars($post['image']); ?>" class="img-fluid rounded" alt="Post image">
            <?php endif; ?>
            <div class="d-flex justify-content-start mt-3">
            <button class="btn btn-outline-primary me-2 like-button <?php echo $post['user_liked'] ? 'active' : ''; ?>" 
            data-post-id="<?php echo $post['id']; ?>"
            onclick="toggleLike(this)">
            <i class="bi bi-hand-thumbs-up-fill"></i>
            <span class="like-count"><?php echo $post['like_count']; ?></span>
          </button>
              <button class="btn btn-outline-primary" onclick="toggleComments(<?php echo $post['id']; ?>)">
                <i class="bi bi-chat"></i> Comentar
              </button>
            </div>
          </div>

          <div class="card-footer">
            <div class="comments-section">
              <!-- Contenedor de comentarios -->
              <div class="comments-container" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                <?php
                $comments_sql = "SELECT comments.*, users.username, users.profile_pic 
                                FROM comments 
                                JOIN users ON comments.user_id = users.id 
                                WHERE post_id = ? 
                                ORDER BY created_at DESC";
                $stmt_comments = $mysqli->prepare($comments_sql);
                $stmt_comments->bind_param("i", $post['id']);
                $stmt_comments->execute();
                $comments = $stmt_comments->get_result();
                $comment_count = $comments->num_rows;
                ?>
                
                <!-- Contador de comentarios -->
                <div class="comments-header mb-3">
                  <small class="text-muted">
                    <?php echo $comment_count; ?> comentario<?php echo $comment_count != 1 ? 's' : ''; ?>
                  </small>
                </div>

                <!-- Lista de comentarios -->
                <?php while ($comment = $comments->fetch_assoc()): ?>
                  <div class="d-flex mb-2">
                    <img src="<?php echo !empty($comment['profile_pic']) ? htmlspecialchars($comment['profile_pic']) : 'images/default-avatar.svg'; ?>" 
                         class="avatar me-2" alt="<?php echo htmlspecialchars($comment['username']); ?>">
                    <div class="bg-light p-2 rounded flex-grow-1">
                      <div class="fw-bold"><?php echo htmlspecialchars($comment['username']); ?></div>
                      <div><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></div>
                      <small class="text-muted">
                        <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                      </small>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>

              <!-- Formulario de comentario -->
              <form action="comment_process.php" method="post" class="comment-form mt-3">
                <div class="d-flex">
                  <img src="<?php echo !empty($current_user['profile_pic']) ? htmlspecialchars($current_user['profile_pic']) : 'images/default-avatar.png'; ?>" 
                       class="avatar me-2" alt="Tu avatar">
                  <div class="input-group">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <input type="text" class="form-control comment-input" 
                           name="comment" 
                           placeholder="Escribe un comentario..." 
                           required
                           onFocus="showComments(<?php echo $post['id']; ?>)">
                    <button class="btn btn-primary comment-btn" type="submit">Enviar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- Sidebar derecha (sugerencias) -->
    <div class="right-sidebar">
      <div class="card suggestions-card">
        <div class="card-body">
          <h5>Sugerencias para ti</h5>
          <?php
          // Consultar sugerencias: usuarios distintos al actual, aleatorios, límite 5
          $suggestions_sql = "SELECT id, username, profile_pic FROM users WHERE id <> ? ORDER BY created_at DESC LIMIT 3";
          $stmt_suggestions = $mysqli->prepare($suggestions_sql);
          $stmt_suggestions->bind_param("i", $_SESSION['user_id']);
          $stmt_suggestions->execute();
          $suggestions = $stmt_suggestions->get_result();
          while ($user = $suggestions->fetch_assoc()):
          ?>
            <div class="user">
              <img src="<?php echo !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'images/default-avatar.svg'; ?>" class="avatar me-2" alt="<?php echo htmlspecialchars($user['username']); ?>">
              <div class="username"><?php echo htmlspecialchars($user['username']); ?></div>
              <div class="action">
                <a href="follow.php?user_id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Seguir</a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    function showComments(postId) {
        var commentsDiv = document.getElementById('comments-' + postId);
        if (commentsDiv.style.display === 'none' || commentsDiv.style.display === '') {
            commentsDiv.style.display = 'block';
        }
    }

    function toggleComments(postId) {
        var commentsDiv = document.getElementById('comments-' + postId);
        if (commentsDiv.style.display === 'none' || commentsDiv.style.display === '') {
            commentsDiv.style.display = 'block';
        } else {
            commentsDiv.style.display = 'none';
        }
    }

   
    function toggleLike(button) {
    const postId = button.dataset.postId;
    const countSpan = button.querySelector('.like-count');
    
    fetch('like_process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `post_id=${postId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'liked') {
            button.classList.add('active');
        } else {
            button.classList.remove('active');
        }
        countSpan.textContent = data.likes;
    })
    .catch(error => console.error('Error:', error));
}




  </script>

  <!-- Bootstrap Bundle JS con Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>