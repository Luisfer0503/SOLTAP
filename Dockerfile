FROM dunglas/frankenphp:php8.2

# Instalar extensiones necesarias
RUN install-php-extensions \
    bcmath \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    redis

# Copiar proyecto
WORKDIR /app
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend
RUN npm install && npm run build

# Comando de arranque
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=3000"]