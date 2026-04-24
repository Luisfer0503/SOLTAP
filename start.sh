#!/bin/sh

# Salir inmediatamente si un comando falla
set -e

echo "🚀 Iniciando configuración de runtime para LFRC..."

# Optimización de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones (opcional, descomentar si es necesario)
# php artisan migrate --force

echo "✅ Sistema listo. Iniciando servidor..."

# Iniciar el servidor (ajusta según tu servidor: php-fpm o php artisan serve)
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}