param([string]$Destination = (Join-Path $PWD 'pagina-encuestas'))

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { throw 'Instala Composer y PHP antes de ejecutar este script.' }
composer create-project laravel/laravel $Destination
$source = $PSScriptRoot
New-Item -ItemType Directory -Force -Path "$Destination\public\css" | Out-Null
Copy-Item "$source\app\*" "$Destination\app" -Recurse -Force
Copy-Item "$source\database\migrations\*" "$Destination\database\migrations" -Force
Copy-Item "$source\resources\views\*" "$Destination\resources\views" -Recurse -Force
Copy-Item "$source\public\css\*" "$Destination\public\css" -Force
Copy-Item "$source\routes\web.php" "$Destination\routes\web.php" -Force
Set-Location $Destination
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
Write-Host 'Listo. Ejecuta: php artisan serve'
