<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>


<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="index.php">Linkfast</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
              aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="profile.php?id=<?php echo $_SESSION['user_id']; ?>">
              <i class="bi bi-person-circle"></i> Mi Perfil
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="search.php">
              <i class="bi bi-search"></i> Buscar
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="chat.php">
              <i class="bi bi-wechat"></i></i> Chats
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">
              <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
