let isPolling = false;
let lastMessageId = 0;

function loadChat(receiverId) {
    if (isPolling) {
        clearInterval(isPolling);
        isPolling = false;
    }
    
    // Actualizar UI
    document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
    document.querySelector(`.chat-item[onclick="loadChat(${receiverId})"]`)?.classList.add('active');
    
    // Limpiar mensajes anteriores
    const messagesContainer = document.getElementById('chat-messages');
    messagesContainer.innerHTML = '';
    
    // Reiniciar lastMessageId
    lastMessageId = 0;
    
    // Cargar mensajes iniciales
    fetchMessages(receiverId);
    
    // Iniciar polling
    startPolling(receiverId);
}

function startPolling(receiverId) {
    if (!isPolling) {
        isPolling = setInterval(() => fetchMessages(receiverId), 3000);
    }
}

async function fetchMessages(receiverId) {
    try {
        const response = await fetch(`includes/get_messages.php?receiver_id=${receiverId}&last_id=${lastMessageId}`);
        const data = await response.json();
        
        if (data.messages && Array.isArray(data.messages)) {
            data.messages.forEach(message => {
                appendMessage(message);
                lastMessageId = Math.max(lastMessageId, message.id);
            });
        }
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

function appendMessage(message) {
    const messagesContainer = document.getElementById('chat-messages');
    
    // Evitar duplicados
    if (document.querySelector(`[data-message-id="${message.id}"]`)) {
        return;
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${message.sender_id === currentUserId ? 'sent' : 'received'}`;
    messageDiv.setAttribute('data-message-id', message.id);
    
    const sanitizedMessage = escapeHtml(message.message);
    
    messageDiv.innerHTML = `
        <div class="message-content">
            <p>${sanitizedMessage}</p>
            <small class="text-muted">${formatDate(message.sent_at)}</small>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('message-form');
    
    if (messageForm) {
        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const messageInput = messageForm.querySelector('input[name="message"]');
            const receiverInput = messageForm.querySelector('input[name="receiver_id"]');
            
            if (!messageInput.value.trim()) return;
            
            try {
                const formData = new FormData();
                formData.append('message', messageInput.value.trim());
                formData.append('receiver_id', receiverInput.value);
                
                const response = await fetch('includes/send_message.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success && result.message) {
                    // Agregar el mensaje al chat
                    appendMessage(result.message);
                    
                    // Limpiar y enfocar el input
                    messageInput.value = '';
                    messageInput.focus();
                    
                    // Actualizar lastMessageId
                    lastMessageId = Math.max(lastMessageId, result.message.id);
                } else {
                    console.error('Error:', result.error);
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });
    }
});