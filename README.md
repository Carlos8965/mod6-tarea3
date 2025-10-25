# 🚀 API de Gestión de Usuarios y Productos - Actividad 3

## 📋 Descripción del Proyecto

Este proyecto implementa **dos APIs completas** según los requisitos de la Actividad 3:

### ✅ Opción 1: API de Gestión de Usuarios
- ✅ **Operaciones CRUD completas** para usuarios
- ✅ **Autenticación con tokens** que expiran cada 5 minutos
- ✅ **Estadísticas de usuarios** por día, semana y mes
- ✅ **Análisis demográfico** y usuarios más activos

### ✅ Opción 2: API de Gestión de Productos
- ✅ **Operaciones CRUD completas** para productos
- ✅ **Sistema de valoraciones** con restricciones de seguridad
- ✅ **Sistema de comentarios** con aprobación
- ✅ **Productos mejor valorados** con cálculo de promedios
- ✅ **Búsqueda y filtrado** avanzado

## 🛠️ Tecnologías Utilizadas

- **Framework:** Laravel 11
- **Base de Datos:** SQLite (desarrollo)
- **Autenticación:** Laravel Sanctum
- **Servidor:** Laravel Herd
- **API:** RESTful con respuestas JSON

## 📂 Estructura de la Base de Datos

### Tablas Principales
- **users** - Información completa de usuarios
- **products** - Catálogo de productos
- **ratings** - Valoraciones de productos (1-5 estrellas)
- **comments** - Comentarios en productos
- **personal_access_tokens** - Tokens de autenticación Sanctum

### Relaciones Implementadas
- Usuario → Múltiples Productos (1:N)
- Usuario → Múltiples Valoraciones (1:N)
- Usuario → Múltiples Comentarios (1:N)
- Producto → Múltiples Valoraciones (1:N)
- Producto → Múltiples Comentarios (1:N)

## 🚀 Instalación y Configuración

### Prerrequisitos
- PHP 8.1+
- Composer
- Laravel Herd (recomendado) o servidor web local

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <repository-url>
   cd api_development
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar archivo de entorno**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Ejecutar migraciones y seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Iniciar servidor (si no usas Herd)**
   ```bash
   php artisan serve
   ```

## 📖 Documentación de la API

### 🌐 URLs de Acceso

- **Desarrollo Local:** `http://localhost:8000`
- **Laravel Herd:** `http://api-development.test`
- **Documentación:** `/api-docs.html`

### 🔐 Autenticación

La API utiliza **Laravel Sanctum** con tokens Bearer que expiran cada **5 minutos**.

**Endpoints de autenticación:**
- `POST /api/auth/register` - Registro de usuario
- `POST /api/auth/login` - Inicio de sesión
- `POST /api/auth/refresh` - Renovar token
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/me` - Información del usuario

### 👥 API de Usuarios

**Operaciones CRUD:**
- `GET /api/users` - Listar usuarios
- `POST /api/users` - Crear usuario
- `GET /api/users/{id}` - Ver usuario
- `PUT /api/users/{id}` - Actualizar usuario
- `DELETE /api/users/{id}` - Eliminar usuario
- `GET /api/users/search` - Buscar usuarios

**Estadísticas:**
- `GET /api/users/stats` - Estadísticas generales
- `GET /api/users/stats/daily` - Registros diarios
- `GET /api/users/stats/weekly` - Registros semanales
- `GET /api/users/stats/monthly` - Registros mensuales
- `GET /api/users/stats/demographics` - Análisis demográfico
- `GET /api/users/stats/most-active` - Usuarios más activos

### 📦 API de Productos

**Operaciones CRUD:**
- `GET /api/products` - Listar productos
- `POST /api/products` - Crear producto
- `GET /api/products/{id}` - Ver producto
- `PUT /api/products/{id}` - Actualizar producto
- `DELETE /api/products/{id}` - Eliminar producto
- `GET /api/products/search` - Buscar productos
- `GET /api/products/top-rated` - Productos mejor valorados

### ⭐ Sistema de Valoraciones

- `GET /api/ratings` - Listar valoraciones
- `POST /api/ratings` - Crear valoración
- `GET /api/ratings/{id}` - Ver valoración
- `PUT /api/ratings/{id}` - Actualizar valoración
- `DELETE /api/ratings/{id}` - Eliminar valoración
- `GET /api/ratings/product/{id}/stats` - Estadísticas de producto

### 💬 Sistema de Comentarios

- `GET /api/comments` - Listar comentarios
- `POST /api/comments` - Crear comentario
- `GET /api/comments/{id}` - Ver comentario
- `PUT /api/comments/{id}` - Actualizar comentario
- `DELETE /api/comments/{id}` - Eliminar comentario
- `PATCH /api/comments/{id}/toggle-approval` - Aprobar/desaprobar

## 🧪 Testing y Ejemplos

### Usuarios de Prueba

| Usuario | Email | Password |
|---------|-------|----------|
| Juan Pérez | juan@example.com | password123 |
| María García | maria@example.com | password123 |
| Carlos Rodríguez | carlos@example.com | password123 |
| Demo User | demo@example.com | demo123 |

### Endpoints de Testing (Sin Autenticación)

Para facilitar las pruebas, incluimos endpoints públicos:

- `GET /api/test/info` - Información de la API
- `GET /api/test/products` - Ver productos
- `GET /api/test/products/{id}` - Ver producto específico
- `GET /api/test/ratings` - Ver valoraciones
- `GET /api/test/comments` - Ver comentarios

### Ejemplo de Uso Completo

```bash
# 1. Registrar usuario
curl -X POST http://api-development.test/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# 2. Crear producto (usar token del paso 1)
curl -X POST http://api-development.test/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "Producto Test",
    "description": "Descripción del producto",
    "price": 99.99,
    "category": "Electrónicos",
    "brand": "TestBrand",
    "sku": "TEST-001",
    "stock": 10
  }'

# 3. Ver estadísticas mensuales
curl -X GET http://api-development.test/api/users/stats/monthly \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🎯 Características Destacadas

### Seguridad Implementada
- ✅ Tokens con expiración de 5 minutos
- ✅ Validación de propietario en operaciones
- ✅ Restricciones: no puedes valorar tus propios productos
- ✅ Sanitización de entrada de datos
- ✅ Autorización basada en roles

### Funcionalidades Avanzadas
- ✅ Cálculo de promedios de valoraciones en tiempo real
- ✅ Estadísticas temporales con datos distribuidos
- ✅ Sistema de búsqueda y filtrado
- ✅ Paginación en todas las listas
- ✅ Respuestas JSON estructuradas y consistentes

### Validaciones y Restricciones
- ✅ Un usuario = una valoración por producto
- ✅ Solo el propietario puede modificar/eliminar
- ✅ Validación de datos de entrada
- ✅ Estados de productos y comentarios
- ✅ Relaciones de integridad referencial

## 📊 Datos de Prueba Incluidos

La base de datos incluye:
- **55+ usuarios** con datos realistas
- **8 productos** de diferentes categorías
- **Múltiples valoraciones** distribuidas
- **Comentarios variados** con aprobaciones
- **Datos temporales** de los últimos 6 meses

## ⚡ Rendimiento y Optimización

- ✅ Consultas optimizadas con Eloquent
- ✅ Carga eager de relaciones
- ✅ Índices en campos de búsqueda
- ✅ Paginación para grandes volúmenes
- ✅ Cache de cálculos complejos

## 🔄 Puntos de Evaluación Cumplidos

### ✅ Implementación de operaciones CRUD
- **Usuarios:** Crear, leer, actualizar, eliminar con validaciones
- **Productos:** CRUD completo con autorización por propietario

### ✅ Autenticación con token de acceso
- Laravel Sanctum configurado
- Tokens con expiración de 5 minutos
- Endpoint de renovación de tokens

### ✅ Diseño y estructura de base de datos
- Migraciones bien estructuradas
- Relaciones 1:N correctamente implementadas
- Restricciones de integridad

### ✅ Documentación y comentarios del código
- PHPDoc en todos los métodos
- Comentarios explicativos en lógica compleja
- README completo con ejemplos

### ✅ Funcionamiento de la API
- Respuestas JSON consistentes
- Códigos de estado HTTP apropiados
- Manejo de errores y validaciones

### ✅ Creación de endpoints para testeo
- Endpoints públicos para testing
- Documentación HTML interactiva
- Datos de prueba incluidos

## 📝 Notas Adicionales

### Estructura del Proyecto
```
app/
├── Http/Controllers/Api/
│   ├── AuthController.php
│   ├── UserController.php
│   ├── UserStatsController.php
│   ├── ProductController.php
│   ├── RatingController.php
│   └── CommentController.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Rating.php
│   └── Comment.php
database/
├── migrations/
└── seeders/
routes/
└── api.php
public/
└── api-docs.html
```

### Próximos Pasos (Opcional)
- [ ] Implementar cache para estadísticas
- [ ] Agregar filtros avanzados
- [ ] Sistema de notificaciones
- [ ] API de subida de imágenes
- [ ] Documentación Swagger/OpenAPI

---

**🎓 Proyecto desarrollado para Actividad 3 - APIs con Laravel**

*Implementación completa de ambas opciones de API con todas las funcionalidades requeridas.*

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
