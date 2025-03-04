# LinkFast - Red Social 🌐

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

## 📋 Requisitos del Sistema
- Servidor web (Apache/Nginx)
- PHP 7.4 o superior
- MySQL 5.7 o superior

## 🗂️ Estructura del Proyecto
```
proyecto-completo/
│
└── linkfast/
    ├── css/
    ├── images/
    ├── includes/
    ├── js/
    ├── templates/
    ├── uploads/
    └── Archivos PHP principales
```

## 🚀 Instalación

### Clonar Repositorio
```bash
git clone https://github.com/[tu-usuario]/linkfast.git
cd linkfast
```

### Configuración de Base de Datos
1. Crear base de datos en MySQL
2. Importar `sql_import/database.sql`
3. Configurar credenciales en `includes/db.php`

### Configuración de Credenciales
- Servidor: http://localhost:8080/
- Usuario: user
- Contraseña: password

## 🔐 Seguridad
- Encriptación de contraseñas
- Protección contra inyección SQL
- Validación de entrada de usuarios
- Implementación de tokens CSRF
- Configuraciones de seguridad en `.htaccess`

## 🖥️ Archivos Principales
- `index.php`: Página principal
- `login.php`: Inicio de sesión
- `register.php`: Registro de usuarios
- `profile.php`: Perfil de usuario
- `post_process.php`: Procesamiento de publicaciones
- `chat.php`: Sistema de mensajería
- `friend_request.php`: Gestión de solicitudes de amistad

## 🛠️ Configuración Adicional
Asegúrate de verificar y configurar los siguientes archivos:
- `includes/db.php`: Configuración de conexión a base de datos
- `.htaccess`: Configuraciones de seguridad y redirección

## 🚧 Próximos Desarrollos
- Mejora del sistema de mensajería
- Implementación de modo oscuro
- Optimización de rendimiento

## 🤝 Contribuciones
Las contribuciones son bienvenidas. Por favor, sigue estos pasos:
1. Haz un fork del repositorio
2. Crea una nueva rama
3. Realiza tus cambios
4. Envía un pull request

