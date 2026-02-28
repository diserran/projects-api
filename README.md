# Prueba Técnica Backend Laravel - Gestión de Proyectos

API RESTful desarrollada con **Laravel 12** para la gestión de proyectos y tareas. Este proyecto implementa una arquitectura orientada a servicios, procesamiento asíncrono de colas y una suite de tests automatizados.

## Tabla de Contenidos

- [Requisitos Previos](#-requisitos-previos)
- [Instalación y Despliegue (Docker)](#-instalación-y-despliegue-docker-recomendado)
- [Instalación Manual](#-instalación-manual-alternativa)
- [Decisiones Técnicas y Arquitectura](#-decisiones-técnicas-y-arquitectura)
- [Endpoints de la API](#-endpoints-de-la-api)
- [Ejecución de Tests](#-ejecución-de-tests)
- [Mejoras Futuras](#-mejoras-futuras)

---

## Requisitos Previos

- **Docker** y **Docker Compose** (Recomendado).
- O bien: PHP 8.2+, Composer, MySQL 8.0+.

---

## Instalación y Despliegue (Docker) - Recomendado

El proyecto incluye una orquestación completa con Docker Compose que levanta la **API**, la **Base de Datos** y el **Worker de Colas** automáticamente.

1. **Clonar el repositorio:**

   ```bash
   git clone https://github.com/diserran/projects-api
   cd projects-api
   ```

2. **Configurar variables de entorno:**
   ```bash
   cp laravel/.env.example laravel/.env
   ```
   **Importante:** Es necesario configurar el host de la base de datos para Docker en el archivo `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=laravel
   DB_PASSWORD=laravelP@ssword123
   QUEUE_CONNECTION=database
   ```
3. **Instalar dependencias:**
   ```bash
   docker compose run --rm app composer install
   ```
4. **Levantar los servicios:**
   ```bash
   docker-compose up -d
   ```
5. **Preparar la base de datos y añadir APP_KEY**:
   ```bash
   docker exec -it api php artisan key:generate
   docker exec -it api php artisan db:seed
   ```

---

## Instalación Manual (Alternativa)

Si prefieres ejecutarlo en tu máquina local sin Docker:

1. Instalar dependencias: `composer install`
2. Configurar `.env` con la base de datos local (`DB_HOST=127.0.0.1`).
3. Generar clave: `php artisan key:generate`
4. Migrar: `php artisan migrate --seed`
5. Levantar servidor: `php artisan serve`
6. **Importante:** En una terminal aparte, levantar el gestor de colas para las notificaciones:
   ```bash
   php artisan queue:work
   ```

---

## Decisiones Técnicas y Arquitectura

Para cumplir con los requisitos de robustez y escalabilidad en el tiempo estimado, se han tomado las siguientes decisiones:

### 1. Patrón Service (Separation of Concerns)

Se ha extraído la lógica de negocio de los Controladores hacia `App\Services\TaskService`.

- **Motivo:** Mantener los controladores "delgados" (Skinny Controllers) y desacoplar la lógica de validación HTTP de la lógica de negocio.
- **Beneficio:** Facilita el testing unitario y permite reutilizar la lógica de creación de tareas desde otros puntos de entrada (CLI, Jobs) sin duplicar código.

### 2. Gestión de Concurrencia (Data Integrity)

Para la regla de negocio _"Máximo 5 tareas en progreso"_:

- Se utiliza **`DB::transaction`** dentro del servicio para asegurar la atomicidad.
- **Optimización:** Se ha añadido un **índice compuesto** en la migración de tareas (`project_id`, `status`) para optimizar las consultas de conteo (`count()`) que se ejecutan en cada inserción.

### 3. Procesamiento Asíncrono (Jobs & Queues)

Cumpliendo el requisito de no bloquear al usuario al completar una tarea:

- Se implementó `NotifyTaskCompletedJob` que simula el envío de una notificación.
- El Job se despacha (`dispatch`) solo después de que la transacción en base de datos se confirma exitosamente.
- Se ha configurado un contenedor Docker dedicado (`queue`) para procesar estos trabajos en segundo plano.
- Utilizar el driver `database` para no tener que depender de otro servicio externo.

### 4. API Resources

Se utilizan **Eloquent Resources** para transformar la respuesta JSON.

- Permite estandarizar formatos de fecha (ISO 8601).
- Evita exponer la estructura interna de la base de datos.
- Previene problemas de rendimiento cargando relaciones condicionalmente.

### 5. Infraestructura y Dockerización

Se tomó la decisión de utilizar una imagen de Docker pre-configurada (laravelfans/laravel) en lugar de construir el entorno desde cero (Dockerfiles vanilla de PHP + Nginx).

Motivos:

- Automatización de Tareas: La imagen incluye entrypoints inteligentes que detectan el entorno y ejecutan automáticamente tareas críticas como las migraciones de base de datos y el caché de configuración al iniciar el contenedor.
- Reducción de Configuración (Boilerplate): Se evita la complejidad de configurar manualmente el servidor web (Nginx/Apache) y sus permisos, permitiendo enfocarse en la lógica de negocio.

---

## Endpoints de la API

Las rutas están versionadas bajo el prefijo `/api/v1`.

### Proyectos

| Método    | Ruta                         | Acción                                |
| --------- | ---------------------------- | ------------------------------------- |
| GET       | `/api/v1/projects`           | Lista todos los proyectos (paginados) |
| POST      | `/api/v1/projects`           | Crea un nuevo proyecto                |
| GET       | `/api/v1/projects/{project}` | Muestra un proyecto con sus tareas    |
| PUT/PATCH | `/api/v1/projects/{project}` | Actualiza un proyecto                 |
| DELETE    | `/api/v1/projects/{project}` | Elimina un proyecto                   |

### Tareas

| Método    | Ruta                               | Acción                    |
| --------- | ---------------------------------- | ------------------------- |
| GET       | `/api/v1/projects/{project}/tasks` | Lista tareas del proyecto |
| POST      | `/api/v1/projects/{project}/tasks` | Crea tarea en el proyecto |
| GET       | `/api/v1/tasks/{task}`             | Muestra una tarea         |
| PUT/PATCH | `/api/v1/tasks/{task}`             | Actualiza una tarea       |
| DELETE    | `/api/v1/tasks/{task}`             | Elimina una tarea         |

---

## Ejecución de Tests

Se ha priorizado el **Feature Testing** para asegurar que los flujos completos de la API funcionan correctamente, cubriendo tanto el "Happy Path" como los casos borde (errores de validación, límites excedidos).

Para ejecutar la suite de tests (dentro del contenedor):

```bash
docker exec -it api php artisan test
```

---

## Mejoras Futuras

Con más tiempo disponible, las siguientes mejoras serían prioritarias para un entorno de producción:

1. **Seguridad:** Implementar **Laravel Sanctum** para autenticación basada en tokens.
2. **Observabilidad:** Integrar **Laravel Telescope** para monitorizar fallos en los Jobs y consultas lentas.
3. **CI/CD:** Configurar GitHub Actions para ejecutar tests y análisis estático (PHPStan/Pint) en cada Pull Request.
4. **Swagger/OpenAPI:** Generar documentación interactiva automática utilizando herramientas como _Scribe_.
5. **Colas**: Cambiar el driver del sistema de colas de `database` a otro pensado para producción como Redis / SQS.
6. **Docker**: Construir una imagen de docker a medidad con permisos y configuraciones más refinadas.
