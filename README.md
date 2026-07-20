# Página de Encuestas

Aplicación Laravel para crear, responder y analizar encuestas. Incluye administración, preguntas de texto/párrafo/opción múltiple/escala, resultados y ubicación opcional.

## Despliegue en Railway

1. Crea un servicio MySQL y conéctalo a este servicio mediante referencias de variables.
2. Configura `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.
3. Railway ejecuta las migraciones y arranca Laravel automáticamente mediante `railway.json`.
4. Después del primer despliegue, visita `/admin/setup` una única vez. El acceso inicial es `admin@encuestas.test` / `admin123`.

## Local

Requiere PHP 8.2+ y Composer. Ejecuta `composer install`, copia `.env.example` a `.env`, configura la base de datos y ejecuta `php artisan key:generate`, `php artisan migrate` y `php artisan serve`.
