# LinkFast - Red Social

## 🌐 Descripción del Proyecto
LinkFast es una plataforma de red social moderna desarrollada con PHP, diseñada para conectar personas, compartir contenido y facilitar la comunicación digital.

## ✨ Características Principales
- Creación y personalización de perfiles
- Publicación de contenido multimedia
- Sistema de amigos y seguimiento
- Mensajería instantánea
- Comentarios
- Notificaciones en tiempo real
- Búsqueda de usuarios
- Privacidad y configuración de cuenta

## 🛠 Tecnologías Utilizadas
- PHP 7.4+
- MySQL
- HTML5
- CSS3
- JavaScript
- AJAX
- Framework (opcional, especificar si se usa)

## 📋 Requisitos del Sistema
- Servidor web (Apache/Nginx)
- PHP 7.4 o superior
- MySQL 5.7 o superior


## 🚀 Instalación

### Clonar Repositorio
```bash
git clone https://github.com/[tu-usuario]/linkfast.git
cd linkfast
```

### Configuración de Base de Datos
1. Crear base de datos en MySQL
2. Importar `sql_import/database.sql`
3. Configurar credenciales en `include/db.php`


## 🔐 Seguridad
- Encriptación de contraseñas
- Protección contra inyección SQL
- Validación de entrada de usuarios
- Implementación de tokens CSRF
- Configuraciones de seguridad en `.htaccess`

cambios en db.php mysqli

http://localhost:8080/
user: user
psswd: password
subir base de datos 
Verificar que la API esté funcionando:

Visita http://localhost:5000/ para ver la página principal
Visita http://localhost:5000/anomalias para ver las anomalías detectadas
Visita http://localhost:5000/estadisticas/Edificio A para ver las estadísticas de una ubicación específica

proyecto-completo/
│
├── linkfast/            # proyecto original de red social
│   ├── css/
│   ├── images/
│   ├── includes/
│   ├── js/
│   ├── templates/
│   ├── uploads/
│   ├── accept_request.php
│   ├── chat.php
│   ├── comment_process.php
│   ├── follow.php
│   ├── friend_request.php
│   ├── friends.php
│   ├── get_profile_pic.php
│   ├── index.php
│   ├── like_process.php
│   ├── login_process.php
│   ├── login.php
│   ├── logout.php
│   ├── post_process.php
│   ├── profile.php
│   ├── register_process.php
│   ├── register.php
│   ├── reject_request.php
│   ├── remove_friend.php
│   ├── search.php
│   └── send_request.php
    
