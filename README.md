# Página de Encuestas — Laravel

Migración de la aplicación Flask del archivo original a Laravel. Incluye panel de administración, creación y activación de encuestas, preguntas de texto/párrafo/opción múltiple/escala, respuestas, resultados y ubicación opcional.

## Instalación

1. Instala PHP 8.2+, Composer y una base de datos MySQL (o usa SQLite).
2. En PowerShell, desde esta carpeta, ejecuta `./install.ps1`.
3. Para MySQL, edita el archivo `.env` creado dentro de `pagina-encuestas` con tus credenciales antes de `php artisan migrate`.
4. Ejecuta `php artisan serve` y abre la dirección indicada.
5. Abre `/admin/setup` una sola vez. Crea el acceso inicial: `admin@encuestas.test` / `admin123`. Inicia sesión en `/admin/login` y cambia esa clave en una futura mejora.

El script crea la base oficial de Laravel y copia encima esta migración. No incluye `vendor/` porque Composer lo genera automáticamente.
