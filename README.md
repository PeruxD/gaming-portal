# 🎮 FORESTER - Gaming Portal

Portal web gaming completo desarrollado con HTML5, PHP y MySQL/MariaDB.

## Características

✅ **Diseño responsivo** - Adaptado para desktop, tablet y móvil
✅ **Sistema de autenticación** - Login y registro de usuarios
✅ **Catálogo de juegos** - Búsqueda y filtrado por categorías
✅ **Ranking de jugadores** - Sistema de puntos y niveles
✅ **Gestión de torneos** - Crear y participar en torneos
✅ **Reseñas de juegos** - Sistema de calificación
✅ **Sistema de logros** - Desbloquea logros
✅ **Newsletter** - Suscripción por email
✅ **API REST** - Endpoints para integración

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o MariaDB 10.3+
- Servidor web (Apache, Nginx)
- Navegador moderno

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/PeruxD/gaming-portal.git
cd gaming-portal
```

### 2. Crear la base de datos

```bash
# Importar el archivo SQL
mysql -u root -p < database/schema.sql
```

O manualmente:
1. Abre phpMyAdmin
2. Importa el archivo `database/schema.sql`
3. Se creará automáticamente la base de datos `gaming_portal`

### 3. Configurar la base de datos

Edita `config/database.php`:

```php
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'tu_contraseña';
$db_name = 'gaming_portal';
```

### 4. Configurar el servidor

**Apache (.htaccess):**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

### 5. Crear carpetas necesarias

```bash
mkdir -p uploads/avatars
mkdir -p uploads/games
mkdir -p uploads/tournaments
chmod 755 uploads
```

## Estructura del Proyecto

```
gaming-portal/
├── index.php              # Página principal
├── config/
│   ├── database.php       # Configuración de BD
│   └── constants.php      # Constantes globales
├── includes/
│   ├── header.php         # Encabezado
│   └── footer.php         # Pie de página
├── auth/
│   ├── login.php          # Página de login
│   ├── register.php       # Página de registro
│   └── logout.php         # Cerrar sesión
├── api/
│   └── subscribe.php      # API de newsletter
├── css/
│   ├── style.css          # Estilos principales
│   └── responsive.css     # Estilos responsivos
├── js/
│   └── main.js            # JavaScript principal
├── database/
│   └── schema.sql         # Estructura de BD
└── uploads/               # Carpeta de subidas
```

## Uso

### Acceder al portal

1. Abre tu navegador
2. Ve a `http://localhost/gaming-portal`
3. Regístrate o inicia sesión
4. ¡Explora los juegos!

### Funcionalidades principales

**Inicio**
- Banner principal con juegos destacados
- Categorías de juegos
- Ranking de jugadores

**Juegos**
- Catálogo completo de juegos
- Búsqueda y filtrado
- Reseñas y calificaciones

**Ranking**
- Top 10 jugadores
- Puntos y niveles
- Posiciones en tiempo real

**Torneos**
- Torneos activos
- Registro de participantes
- Premios y recompensas

## Ejemplos de Uso

### Crear un nuevo juego (Admin)

```php
$title = "Mi Juego";
$description = "Descripción del juego";
$category_id = 1;
$rating = 8.5;

$query = "INSERT INTO games (title, description, category_id, rating) 
         VALUES ('$title', '$description', $category_id, $rating)";
mysqli_query($conn, $query);
```

### Obtener ranking de jugadores

```php
$query = "SELECT u.username, p.points, p.level, p.rank 
         FROM players p 
         JOIN users u ON p.user_id = u.id 
         ORDER BY p.points DESC LIMIT 10";
$result = mysqli_query($conn, $query);
```

## API Endpoints

### Newsletter

**POST** `/api/subscribe.php`
```json
{
  "email": "usuario@example.com"
}
```

## Seguridad

⚠️ **Importante para producción:**

1. **Cambiar contraseñas predeterminadas**
2. **Usar HTTPS**
3. **Validar y sanitizar inputs**
4. **Usar tokens CSRF**
5. **Rate limiting en APIs**
6. **Encriptar datos sensibles**
7. **Backups regulares**

## Troubleshooting

### Error de conexión a BD
- Verifica que MySQL está ejecutándose
- Comprueba credenciales en `config/database.php`
- Asegúrate que la BD existe

### Imágenes no se cargan
- Verifica permisos de carpeta `uploads/`
- Comprueba rutas en el HTML

### CSS no aplica
- Limpia caché del navegador (Ctrl+F5)
- Verifica ruta de los archivos CSS

## Créditos

Desarrollado por **PeruxD** 🎮

## Licencia

MIT License - Libre para usar, modificar y distribuir

## Soporte

Para reportar bugs o sugerencias:
- 📧 Email: support@forester.com
- 💬 Discord: [Servidor de Discord]
- 🐙 GitHub Issues: [Gaming Portal Issues]

---

**¡Únete a la comunidad gaming más épica!** 🚀
