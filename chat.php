<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/navbar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

// Verificar si los usuarios son amigos
function areUsersFriends($mysqli, $user_id, $receiver_id) {
    $query = "SELECT COUNT(*) as count FROM friend_requests 
              WHERE ((sender_id = ? AND receiver_id = ?) 
              OR (receiver_id = ? AND sender_id = ?))
              AND status = 'accepted'";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iiii', $user_id, $receiver_id, $user_id, $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

// Si hay un receiver_id, verificar que sean amigos
if ($receiver_id) {
    if (!areUsersFriends($mysqli, $user_id, $receiver_id)) {
        $error_message = "No puedes enviar mensajes a este usuario.";
        $receiver_id = null;
    }
}

// Obtener la lista de amigos y sus últimos mensajes
$query = "SELECT 
            u.id, 
            u.username, 
            u.profile_pic,
            (SELECT message FROM messages 
             WHERE (sender_id = u.id AND receiver_id = ?) 
                OR (receiver_id = u.id AND sender_id = ?) 
             ORDER BY sent_at DESC LIMIT 1) as last_message,
            (SELECT sent_at FROM messages 
             WHERE (sender_id = u.id AND receiver_id = ?) 
                OR (receiver_id = u.id AND sender_id = ?) 
             ORDER BY sent_at DESC LIMIT 1) as last_message_time
          FROM users u
          INNER JOIN friend_requests fr ON 
            (fr.sender_id = u.id AND fr.receiver_id = ?) 
            OR (fr.receiver_id = u.id AND fr.sender_id = ?)
          WHERE u.id != ?
          AND fr.status = 'accepted'
          ORDER BY last_message_time DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('iiiiiii', $user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Si hay un receiver_id válido, obtener los mensajes de la conversación
if ($receiver_id) {
    $messages_query = "SELECT 
                        m.*, 
                        u.username, 
                        u.profile_pic 
                      FROM messages m 
                      INNER JOIN users u ON m.sender_id = u.id 
                      WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                         OR (m.sender_id = ? AND m.receiver_id = ?) 
                      ORDER BY m.sent_at ASC";
    
    $stmt = $mysqli->prepare($messages_query);
    if ($stmt) {
        $stmt->bind_param('iiii', $user_id, $receiver_id, $receiver_id, $user_id);
        if ($stmt->execute()) {
            $initial_messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            error_log("Error executing statement: " . $stmt->error);
            $error_message = "Error al cargar los mensajes";
        }
    } else {
        error_log("Error preparing statement: " . $mysqli->error);
        $error_message = "Error al cargar los mensajes";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Mi Red Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="css/navbar.css" rel="stylesheet"> <!-- O la ruta donde tengas tus estilos personalizados -->
    <style>
    .chat-container {
        display: flex;
        height: calc(100vh - 100px);
        margin-top: 20px;
    }

    .chat-list {
        width: 300px;
        border-right: 1px solid #dee2e6;
        overflow-y: auto;
        display: block; /* Asegura layout vertical */
    }

    .chat-section {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .chat-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #dee2e6;
        cursor: pointer;
        width: 100%; /* Ocupa todo el ancho disponible */
        box-sizing: border-box; /* Incluye padding en el ancho */
        max-width: 300px; /* Coincide con el ancho de chat-list */
    }

    .chat-item:hover {
        background-color: #f8f9fa;
    }

    .chat-item.active {
        background-color: #e9ecef;
    }

    .chat-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
        flex-shrink: 0; /* Evita que la imagen se reduzca */
    }

    .chat-info {
        flex: 1;
        min-width: 0; /* Permite truncamiento de texto */
    }

    .chat-info h6 {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 4px;
        max-width: 200px; /* Ajuste para el truncamiento */
    }

    .chat-info p {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0;
        font-size: 0.9em;
    }

    .chat-info small {
        display: block;
        font-size: 0.8em;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background-color: #f8f9fa;
    }

    .message {
        margin-bottom: 15px;
        max-width: 70%;
    }

    .message.sent {
        margin-left: auto;
    }

    .message-content {
        padding: 10px;
        border-radius: 15px;
        background-color: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .message.sent .message-content {
        background-color: #007bff;
        color: white;
    }

    .message.sent .message-content .text-muted {
        color: rgba(255,255,255,0.7) !important;
    }

    .chat-input {
        padding: 20px;
        background-color: #fff;
        border-top: 1px solid #dee2e6;
    }

    .chat-input form {
        display: flex;
        gap: 10px;
    }

    .chat-input input {
        flex: 1;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        color: #6c757d;
        padding: 20px;
    }

    .empty-state i {
        font-size: 3em;
        margin-bottom: 15px;
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
</style>
</head>
<body>
    <div class="container-fluid">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger mt-3">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <div class="chat-container">
            <!-- Lista de conversaciones -->
            <div class="chat-list">
                <div class="chat-list-header p-3">
                    <h5 class="mb-0">Conversaciones</h5>
                </div>
                
                <?php if (empty($conversations)): ?>
                    <div class="empty-state">
                        <i class="bi bi-chat-dots"></i>
                        <p>No tienes amigos aún.</p>
                        <a href="search.php" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar amigos
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
    <div class="chat-item <?php echo ($receiver_id == $conv['id']) ? 'active' : ''; ?>" 
         onclick="window.location.href='chat.php?user_id=<?php echo $conv['id']; ?>'">
        <img src="<?php echo get_profile_pic_url($conv['profile_pic']); ?>" 
             class="chat-avatar"
             alt="<?php echo htmlspecialchars($conv['username']); ?>">
        <div class="chat-info">
            <h6 class="mb-1"><?php echo htmlspecialchars($conv['username']); ?></h6>
            <?php if (isset($conv['last_message'])): ?>
                <p class="text-truncate mb-0"><?php echo htmlspecialchars($conv['last_message']); ?></p>
                <small class="text-muted"><?php echo format_date($conv['last_message_time']); ?></small>
            <?php else: ?>
                <p class="text-muted mb-0">Iniciar conversación</p> <!-- este funciona -->
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?> <!-- CAMBIAR HASTA AQUI PARA QUITAR DEBUG 2/2/2025 -->
                <?php endif; ?>
            </div>

            <!-- Sección de chat -->
            <div class="chat-section">
                <?php if (!$receiver_id): ?>
                    <div class="empty-state">
                        <i class="bi bi-chat-dots"></i>
                        <p>Selecciona una conversación para comenzar</p>
                    </div>
                <?php else: ?>
                    <div class="chat-header p-3 border-bottom">
                        <h6 class="mb-0">Chat con <?php echo htmlspecialchars(get_username($receiver_id)); ?></h6>
                    </div>
                    <div class="chat-messages" id="chat-messages">
                        <?php if (isset($initial_messages)): ?>
                            <?php foreach ($initial_messages as $msg): ?>
                                <div class="message <?php echo ($msg['sender_id'] == $user_id) ? 'sent' : 'received'; ?>" 
                                     data-message-id="<?php echo $msg['id']; ?>">
                                    <div class="message-content">
                                        <p class="mb-1"><?php echo htmlspecialchars($msg['message']); ?></p>
                                        <small class="text-muted"><?php echo format_date($msg['sent_at']); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="chat-input">
                        <form id="message-form">
                            <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
                            <input type="text" name="message" class="form-control" 
                                   placeholder="Escribe un mensaje..." required>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
   const currentUserId = <?php echo $_SESSION['user_id']; ?>;
const currentReceiverId = <?php echo $receiver_id ?? 'null'; ?>;
let lastMessageId = 0;
let isScrolledToBottom = true;

function initializeChat() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        // Obtener el último ID de mensaje
        const messages = document.querySelectorAll('.message');
        if (messages.length > 0) {
            const lastMessage = messages[messages.length - 1];
            lastMessageId = parseInt(lastMessage.dataset.messageId) || 0;
        }

        // Scroll inicial al fondo
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Detectar scroll
        chatMessages.addEventListener('scroll', function() {
            isScrolledToBottom = (chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight) < 20;
        });

        // Comprobar nuevos mensajes cada 2 segundos
        setInterval(checkNewMessages, 2000);
    }

    // Configurar el formulario de mensajes
    const messageForm = document.getElementById('message-form');
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await sendMessage(this);
        });
    }

    // Actualizar lista de conversaciones
    setInterval(updateConversationsList, 5000);
}

async function updateConversationsList() {
    try {
        const response = await fetch('api/get_conversations.php');
        const data = await response.json();
        
        if (data.success && data.conversations) {
            const chatList = document.querySelector('.chat-list');
            const conversationsContainer = chatList.querySelector(':scope > div:nth-child(2)');
            
            // Crear un mapa de las conversaciones actuales
            const currentConversations = new Map();
            conversationsContainer.querySelectorAll('.chat-item').forEach(item => {
                const userId = item.querySelector('.chat-avatar').alt;
                currentConversations.set(userId, item);
            });
            
            // Actualizar o agregar nuevas conversaciones
            data.conversations.forEach(conv => {
                const userId = conv.username;
                let chatItem = currentConversations.get(userId);
                
                if (chatItem) {
                    // Actualizar la información de la conversación existente
                    chatItem.querySelector('.chat-info h6').textContent = escapeHtml(conv.username);
                    chatItem.querySelector('.chat-info p').textContent = conv.last_message ? escapeHtml(conv.last_message) : 'Iniciar conversación';
                    chatItem.querySelector('.chat-info small').textContent = conv.last_message ? formatDate(conv.last_message_time) : '';
                } else {
                    // Crear un nuevo elemento de conversación
                    chatItem = document.createElement('div');
                    chatItem.className = `chat-item ${currentReceiverId == conv.id ? 'active' : ''}`;
                    chatItem.onclick = () => window.location.href = `chat.php?user_id=${conv.id}`;
                    
                    const avatarImg = document.createElement('img');
                    avatarImg.src = `get_profile_pic.php?pic=${conv.profile_pic}`;
                    avatarImg.alt = escapeHtml(conv.username);
                    avatarImg.className = 'chat-avatar';
                    
                    const chatInfo = document.createElement('div');
                    chatInfo.className = 'chat-info';
                    
                    const usernameEl = document.createElement('h6');
                    usernameEl.className = 'mb-1';
                    usernameEl.textContent = escapeHtml(conv.username);
                    
                    // Actualizar la información de la conversación existente
                    chatItem.querySelector('.chat-info h6').textContent = escapeHtml(conv.username);
                    chatItem.querySelector('.chat-info p').textContent = conv.last_message ? escapeHtml(conv.last_message) : 'Iniciar conversación';
                    chatItem.querySelector('.chat-info small').textContent = conv.last_message ? formatDate(conv.last_message_time) : '';
                    
                    const lastMessageTimeEl = document.createElement('small');
                    lastMessageTimeEl.className = 'text-muted';
                    lastMessageTimeEl.textContent = conv.last_message ? formatDate(conv.last_message_time) : '';
                    
                    chatInfo.appendChild(usernameEl);
                    chatInfo.appendChild(lastMessageEl);
                    chatInfo.appendChild(lastMessageTimeEl);
                    
                    chatItem.appendChild(avatarImg);
                    chatItem.appendChild(chatInfo);
                    
                    conversationsContainer.appendChild(chatItem);
                }
            });
            
            // Eliminar las conversaciones que ya no existen
            currentConversations.forEach(item => {
                if (!data.conversations.find(conv => conv.username === item.querySelector('.chat-avatar').alt)) {
                    item.remove();
                }
            });
        }
    } catch (error) {
        console.error('Error actualizando conversaciones:', error);
    }
}

async function sendMessage(form) {
    const messageInput = form.querySelector('input[name="message"]');
    const message = messageInput.value.trim();
    
    if (!message) return;

    try {
        const response = await fetch('api/send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                receiver_id: currentReceiverId,
                message: message
            })
        });

        const data = await response.json();
        
        if (data.success) {
            messageInput.value = '';
            appendMessage({
                id: data.message_id,
                message: message,
                sender_id: currentUserId,
                sent_at: new Date().toISOString()
            });
            //scrollToBottom();
            setTimeout(scrollToBottom, 0);
        } else {
            alert('Error al enviar el mensaje');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al enviar el mensaje');
    }
     // Actualizar conversaciones inmediatamente
     await updateConversationsList();
        scrollToBottom();
}

async function checkNewMessages() {
    if (!currentReceiverId) return;

    try {
        const response = await fetch(`api/get_messages.php?receiver_id=${currentReceiverId}&last_id=${lastMessageId}`);
        const data = await response.json();
        
        if (data.success && data.messages) {
            data.messages.forEach(message => {
                if (!document.querySelector(`.message[data-message-id="${message.id}"]`)) {
                    appendMessage(message);
                    lastMessageId = Math.max(lastMessageId, message.id);
                }
            });

            if (isScrolledToBottom) {
                scrollToBottom();
            }
        }
    } catch (error) {
        console.error('Error obteniendo mensajes:', error);
    }
}

// Función para formatear fechas
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const minutes = Math.floor(diffTime / (1000 * 60));

    if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    } else if (minutes < 60) {
        return `Hace ${minutes} minutos`;
    } else {
        return date.toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            year: '2-digit', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }
}

// Función para actualizar timestamps
function updateTimestamps() {
    const timestamps = document.querySelectorAll('.message .text-muted');
    timestamps.forEach(timestamp => {
        const messageId = timestamp.closest('.message').dataset.messageId;
        const message = document.querySelector(`.message[data-message-id="${messageId}"]`);
        if (message) {
            const sentAt = message.dataset.sentAt;
            if (sentAt) {
                timestamp.textContent = formatDate(sentAt);
            }
        }
    });
}

// Función para agregar mensajes
function appendMessage(message) {
    const chatMessages = document.getElementById('chat-messages');
    if (!chatMessages) return;

    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${message.sender_id == currentUserId ? 'sent' : 'received'}`;
    messageDiv.dataset.messageId = message.id;
    
    // Usar la fecha exacta del mensaje en lugar de "Ahora mismo"
    const sentDate = new Date(message.sent_at);
    const formattedTime = sentDate.toLocaleTimeString('es-ES', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });

    messageDiv.innerHTML = `
        <div class="message-content">
            <p class="mb-1">${escapeHtml(message.message)}</p>
            <small class="text-muted">${formattedTime}</small>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    
    /*if (message.sender_id != currentUserId) {
        playNotificationSound();
    }*/
    if (message.sender_id === currentUserId) {
        setTimeout(scrollToBottom, 0);
    }
}


// Función para verificar si el usuario está en el fondo del chat
function checkIfScrolledToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    const threshold = 100;
    return chatMessages && (chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight) < threshold;
}

// Función para hacer scroll hasta el final del chat
function scrollToBottom() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTo({
            top: chatMessages.scrollHeight,
            behavior: 'smooth'
        });

        //chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// Función para escapar HTML
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Función para obtener el último ID de mensaje
function getLastMessageId() {
    const messages = document.querySelectorAll('.message');
    if (messages.length > 0) {
        const lastMessage = messages[messages.length - 1];
        return parseInt(lastMessage.dataset.messageId) || 0;
    }
    return 0;
}

// Función para inicializar el chat
function initializeChat() {
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');

    if (chatMessages) {
        lastMessageId = getLastMessageId();
        scrollToBottom();
        
        // Verificar mensajes cada 2 segundos
        setInterval(checkNewMessages, 2000);

        // Actualizar timestamps cada minuto
        setInterval(updateTimestamps, 60000);

        // Actualizar conversaciones cada 5 segundos
        setInterval(updateConversationsList, 5000);
        
        // Detectar scroll
        chatMessages.addEventListener('scroll', function() {
            isScrolledToBottom = checkIfScrolledToBottom();
        });
    }

    // Configurar el formulario de mensajes
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            await sendMessage(this);
        });
    }
}

// Iniciar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeChat);
    </script>
</body>
</html>