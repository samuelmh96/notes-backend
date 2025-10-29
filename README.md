# Notes Backend API

API RESTful para gestión de notas con sistema de tags, construida con Laravel 11 y SQLite.

## 🚀 Tecnologías

- **Laravel 11** - Framework PHP moderno
- **SQLite** - Base de datos ligera
- **Laravel Sanctum** - Autenticación API
- **Eloquent ORM** - Manejo de base de datos

## 📋 Requisitos previos

- PHP 8.2+
- Composer
- SQLite3

## 🔧 Instalación
```bash
# Clonar el repositorio
git clone https://github.com/TU_USUARIO/notes-backend.git

# Entrar al directorio
cd notes-backend

# Instalar dependencias
composer install

# Copiar archivo de configuración
cp .env.example .env

# Generar key de la aplicación
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Iniciar servidor de desarrollo
php artisan serve
```

## 🏗️ Estructura de la base de datos

### Tabla: notes
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| title | string | Título de la nota |
| content | text | Contenido de la nota |
| created_at | timestamp | Fecha de creación |
| updated_at | timestamp | Fecha de actualización |

### Tabla: tags
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| name | string | Nombre del tag |
| created_at | timestamp | Fecha de creación |
| updated_at | timestamp | Fecha de actualización |

### Tabla: note_tag (relación many-to-many)
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| note_id | bigint | FK a notes |
| tag_id | bigint | FK a tags |

## 📡 Endpoints de la API

### Notas

#### Listar todas las notas
```http
GET /api/notes
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "title": "Mi primera nota",
    "content": "Contenido de ejemplo",
    "tags": [
      {
        "id": 1,
        "name": "importante"
      }
    ],
    "created_at": "2024-10-29T10:00:00.000000Z",
    "updated_at": "2024-10-29T10:00:00.000000Z"
  }
]
```

#### Crear una nota
```http
POST /api/notes
Content-Type: application/json

{
  "title": "Título de la nota",
  "content": "Contenido de la nota",
  "tags": [1, 2]  // opcional: array de IDs de tags
}
```

**Respuesta:**
```json
{
  "id": 1,
  "title": "Título de la nota",
  "content": "Contenido de la nota",
  "tags": [],
  "created_at": "2024-10-29T10:00:00.000000Z",
  "updated_at": "2024-10-29T10:00:00.000000Z"
}
```

#### Ver una nota específica
```http
GET /api/notes/{id}
```

#### Actualizar una nota
```http
PUT /api/notes/{id}
Content-Type: application/json

{
  "title": "Título actualizado",
  "content": "Contenido actualizado"
}
```

#### Eliminar una nota
```http
DELETE /api/notes/{id}
```

## 🔐 CORS

CORS está configurado para aceptar peticiones desde:
- `http://localhost:5173` (Frontend Vue)
- `http://localhost:3000`

Puedes modificar la configuración en `config/cors.php`

## 🎯 Modelos y Relaciones

### Modelo Note
```php
// Una nota puede tener muchos tags
public function tags()
{
    return $this->belongsToMany(Tag::class);
}
```

### Modelo Tag
```php
// Un tag puede pertenecer a muchas notas
public function notes()
{
    return $this->belongsToMany(Note::class);
}
```

## 🧪 Testing
```bash
# Ejecutar tests
php artisan test
```

## 📦 Comandos útiles
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear

# Crear nuevo modelo con migración
php artisan make:model NombreModelo -m

# Crear nuevo controller
php artisan make:controller NombreController --api

# Ver rutas disponibles
php artisan route:list
```

## 🔗 Frontend

Este backend funciona con el frontend Vue:
- Repositorio: https://github.com/samuelmh96/notes-frontend
