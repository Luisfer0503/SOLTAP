FROM dunglas/frankenphp:php8.2

# 🔥 ESTA LÍNEA ES LA CLAVE
RUN install-php-extensions \
    bcmath \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    redis

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN npm install && npm run build

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=3000"]