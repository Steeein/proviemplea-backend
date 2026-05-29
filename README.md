# ProviEmplea — Backend API REST

Backend de la plataforma **ProviEmplea**, un sistema de intermediación laboral que conecta personas en búsqueda de empleo con empresas que ofrecen vacantes. Construido con **Laravel 11**, contenerizado con **Docker** y documentado con **Swagger / OpenAPI 3.0**.

---

## Tecnologías principales

| Capa | Tecnología |
|---|---|
| Lenguaje | PHP 8.2 |
| Framework | Laravel 11 |
| Base de datos | MySQL 8.0 |
| Servidor web | Nginx 1.27 Alpine |
| Contenedores | Docker + Docker Compose |
| Documentación API | L5-Swagger (OpenAPI 3.0) |
| Testing | PHPUnit 11 |

---

## Requisitos previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado y ejecutándose
- [Git](https://git-scm.com/)
- Puerto **8080** y **3306** libres en tu máquina

---

## Instalación y puesta en marcha

### 1. Clonar el repositorio

```bash
git clone https://github.com/Steeein/proviemplea-backend.git
cd proviemplea-backend
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` y asegúrate de que las variables de base de datos coincidan con las del `docker-compose.yaml`:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=proviemplea
DB_USERNAME=proviemplea_user
DB_PASSWORD=proviemplea_pass
```

### 3. Levantar los contenedores

```bash
docker compose up -d --build
```

Esto levanta tres servicios:
- `proviemplea_app` — PHP-FPM con Laravel
- `proviemplea_web` — Nginx en el puerto 8080
- `proviemplea_db` — MySQL 8.0 en el puerto 3306

### 4. Generar la clave de aplicación

```bash
docker compose exec app php artisan key:generate
```

### 5. Ejecutar las migraciones

```bash
docker compose exec app php artisan migrate
```

### 6. (Opcional) Ejecutar los seeders

```bash
docker compose exec app php artisan db:seed
```

---

## Verificar que el sistema está corriendo

Abre tu navegador o ejecuta:

```bash
curl http://localhost:8080/api/health
```

Respuesta esperada:

```json
{
  "status": "ok",
  "timestamp": "2026-05-28T..."
}
```

---

## Documentación de la API (Swagger UI)

Una vez los contenedores estén corriendo, la documentación interactiva está disponible en:

```
http://localhost:8080/api/documentation
```

Para regenerar el archivo de especificación OpenAPI desde las anotaciones del código:

```bash
docker compose exec app php artisan l5-swagger:generate
```

---

## Endpoints disponibles

Base URL: `http://localhost:8080/api`

Rate limiting: **60 solicitudes por minuto** por IP.

### Health

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/health` | Estado del sistema |

### Personas (Talentos)

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/personas` | Listar CV ciegos (vitrina pública) |
| POST | `/personas` | Registrar nuevo talento |
| GET | `/personas/{id}` | Ver perfil completo (uso admin) |
| PUT | `/personas/{id}` | Actualizar perfil |
| DELETE | `/personas/{id}` | Desactivar perfil (soft delete) |
| PATCH | `/personas/{id}/validar` | Activar/desactivar visibilidad en vitrina |

### Empresas

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/empresas` | Listar empresas |
| POST | `/empresas` | Registrar empresa |
| GET | `/empresas/{id}` | Ver empresa |
| PUT | `/empresas/{id}` | Actualizar empresa |
| DELETE | `/empresas/{id}` | Eliminar empresa |
| PATCH | `/empresas/{id}/validar` | Activar/desactivar validación |

### Administración

| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/admin/contactos` | Listar solicitudes de contacto |
| POST | `/admin/contactos` | Crear solicitud de contacto |
| PATCH | `/admin/contactos/{id}/estado` | Actualizar estado de contacto |
| GET | `/admin/estadisticas` | Estadísticas generales del sistema |

---

## Estructura del proyecto

```
proviemplea-backend/
├── app/
│   ├── Http/Controllers/       # Controladores REST + Schemas Swagger
│   ├── Models/                 # Modelos Eloquent (Persona, Empresa, ContactoSolicitado)
│   ├── Providers/              # AppServiceProvider (rate limiting)
│   └── Traits/                 # ApiResponse (respuestas JSON estandarizadas)
├── config/
│   └── l5-swagger.php          # Configuración de Swagger
├── database/
│   ├── migrations/             # Migraciones de la base de datos
│   └── seeders/
├── docker/
│   ├── nginx/default.conf      # Configuración de Nginx
│   └── php/Dockerfile          # Imagen PHP-FPM personalizada
├── routes/
│   └── api.php                 # Definición de todos los endpoints
├── storage/api-docs/           # Especificación OpenAPI generada
├── docker-compose.yaml
└── .env.example
```

---

## Comandos útiles

```bash
# Ver logs en tiempo real
docker compose logs -f

# Acceder al contenedor de la app
docker compose exec app bash

# Ejecutar tests
docker compose exec app php artisan test

# Detener los contenedores
docker compose down

# Detener y eliminar volúmenes (borra la base de datos)
docker compose down -v
```

---

## Variables de entorno relevantes

| Variable | Descripción | Valor por defecto |
|---|---|---|
| `APP_ENV` | Entorno de la aplicación | `local` |
| `APP_DEBUG` | Modo debug | `true` |
| `DB_HOST` | Host de la base de datos | `db` |
| `DB_DATABASE` | Nombre de la base de datos | `proviemplea` |
| `DB_USERNAME` | Usuario de la base de datos | `proviemplea_user` |
| `DB_PASSWORD` | Contraseña de la base de datos | `proviemplea_pass` |
| `L5_SWAGGER_GENERATE_ALWAYS` | Regenerar Swagger en cada request | `true` (dev) |

---

## Autores

- **Diego Abaroa Ramos** — [@Steeein](https://github.com/Steeein)

---

## Licencia

Este proyecto fue desarrollado como evaluación académica para el curso de **Back End — Unidad 3**, año 2026.
